<?php

declare(strict_types=1);

namespace Phico\View\Rapier;


class Renderer
{
    protected Cache $cache;
    protected Templates $templates;
    // use cached templates (default) or not (dev mode)
    private bool $use_cache;
    // holdes built in and custom directives
    protected array $directives = [];
    // track the @sections
    protected array $sections = [];
    protected array $section_stack = [];
    protected array $stacks = [];
    // track the @once sections
    protected array $once_stack = [];
    // track which @once sections have been rendered
    protected array $once_rendered = [];
    // track verbatim placeholders
    protected array $verbatim = [];
    // holds the name of the parent template to extend
    protected null|string $extends = null;
    // holds the content as it is assembled
    protected string $content = '';


    public function __construct(Cache $cache, Templates $templates, array $directives = [], bool $use_cache = true)
    {
        // cache is required to fetch partials
        $this->cache = $cache;
        // template is required to fetch uncompiled templates
        $this->templates = $templates;
        // set passed directives
        $this->directives = $directives;
        // set use_cache flag
        $this->use_cache = $use_cache;
        // set content;
        $this->content = "";
        // set built in directives
        $this->setBuiltinDirectives();
    }

    public function render(string $template, array $data = [], bool $is_string = false): string
    {
        $this->content = ($is_string) ? $template : $this->templates->get($template);
        $this->compile($data);

        while (!is_null($this->extends)) {
            // set parent to the name of template being extended
            $parent = $this->extends;
            // reset for handling multi-level inheritance
            $this->extends = null;
            // fetch parent content
            $content = $this->cache->get($parent) ?? $this->templates->get($parent);
            // replace content with parent (parent sections are already saved in stacks)
            $this->content = $content;
            // prcess parent
            $this->compile($data);
            // store parent template in cache
            $this->cache->put($parent, $content);
        }

        // process the regex replacements once all child/parent layers are resolved
        $this->content = $this->processReplacements($this->content);
        // always restore verbatim code last
        $this->content = $this->restoreVerbatim($this->content);

        // store template in cache
        $this->cache->put($template, $this->content);
        // merge data fields into rendered template
        $this->compile($data);

        return $this->content;
    }

    public function directive(string $name, callable $handler)
    {
        $this->directives[$name] = $handler;
    }

    protected function setBuiltinDirectives()
    {
        $this->directive('section', function ($expression) {
            return "<?php \$this->startSection{$expression}; ?>";
        });

        $this->directive('endsection', function () {
            return "<?php \$this->endSection(); ?>";
        });

        $this->directive('append', function () {
            return "<?php \$this->appendSection(); ?>";
        });

        $this->directive('yield', function ($expression) {
            return "<?php echo \$this->yieldSection{$expression}; ?>";
        });

        $this->directive('extends', function ($expression) {
            return "<?php \$this->extends{$expression}; ?>";
        });

        $this->directive('show', function () {
            return "<?php \$this->showSection(); ?>";
        });

        $this->directive('verbatim', function () {
            return '@verbatim';
        });

        $this->directive('endverbatim', function () {
            return '@endverbatim';
        });

        $this->directive('include', function ($expression) {
            return "<?php \$this->includeTemplate{$expression}; ?>";
        });

        $this->directive('includeIf', function ($expression) {
            return "<?php \$this->includeTemplateIf{$expression}; ?>";
        });

        $this->directive('includeWhen', function ($expression) {
            return "<?php \$this->includeTemplateWhen{$expression}; ?>";
        });

        $this->directive('push', function ($expression) {
            return "<?php \$this->startPush{$expression}; ?>";
        });

        $this->directive('endpush', function () {
            return "<?php \$this->endPush(); ?>";
        });

        $this->directive('stack', function ($expression) {
            return "<?php echo \$this->yieldStack{$expression}; ?>";
        });

        $this->directive('once', function () {
            return "<?php if (!\$this->hasRenderedOnce()): ?>";
        });

        $this->directive('endonce', function () {
            return "<?php \$this->endOnce(); endif; ?>";
        });
    }

    protected function startSection(string $name, string $default = '')
    {
        $this->section_stack[] = $name;
        $this->sections[$name] = $default;
        ob_start();
    }
    protected function endSection()
    {
        $last = array_pop($this->section_stack);
        // fetch content from output buffer
        $content = trim(ob_get_clean());
        // the sections array will contain the default, use it if buffer was empty
        $this->sections[$last] = (!empty($content)) ? $content : $this->sections[$last];
    }
    protected function appendSection()
    {
        $last = array_pop($this->section_stack);
        $this->sections[$last] .= ob_get_clean();
    }
    protected function showSection()
    {
        $last = end($this->section_stack);
        $this->endSection(); // End the current section
        echo $this->yieldSection($last); // Display the section content immediately
    }
    protected function yieldSection(string $name): string
    {
        return $this->sections[$name] ?? '';
    }
    protected function extends(string $template)
    {
        $this->extends = $template;
    }
    protected function includeTemplate(string $template)
    {
        $this->render($template);
    }
    protected function includeTemplateIf(string $template)
    {
        if (file_exists($template)) {
            $this->render($template);
        }
    }
    protected function includeTemplateWhen(bool $condition, string $template)
    {
        if ($condition) {
            $this->render($template);
        }
    }
    public function startPush(string $name)
    {
        $this->section_stack[] = $name;
        if (!isset($this->stacks[$name])) {
            $this->stacks[$name] = '';
        }
        ob_start();
    }
    public function endPush()
    {
        $last = array_pop($this->section_stack);
        $this->stacks[$last] .= ob_get_clean();
    }
    public function hasRenderedOnce(string $name): bool
    {
        return in_array($name, $this->once_rendered);
    }
    public function endOnce()
    {
        // put the section name on the already rendered stack
        $this->once_rendered[] = array_pop($this->once_stack);
    }

    protected function extractVerbatim(string $content): string
    {
        $pattern = '/@verbatim(.*?)@endverbatim/s';
        $index = count($this->verbatim);

        // First pass: replace @verbatim blocks with placeholders
        $content = preg_replace_callback($pattern, function ($matches) use (&$placeholders, &$index) {
            $placeholder = "<!-- VERBATIM_PLACEHOLDER_{$index} -->";
            $placeholders[$placeholder] = $matches[1];
            $index++;
            return $placeholder;
        }, $content);

        return $content;
    }
    protected function restoreVerbatim(string $content): string
    {
        foreach ($this->verbatim as $match => $replace) {
            $encoded = htmlspecialchars($replace, ENT_QUOTES, 'UTF-8');
            $content = str_replace($match, $encoded, $content);
            unset($this->verbatim[$match]);
        }

        return $content;
    }

    protected function processDirectives(string $content): string
    {
        foreach ($this->directives as $name => $handler) {
            $pattern = "/@{$name}(\((.*?)\))?/";
            $content = preg_replace_callback($pattern, function ($matches) use ($handler) {
                $expression = isset($matches[2]) ? "({$matches[2]})" : '()';
                return call_user_func($handler, $expression);
            }, $content);
        }
        return $content;
    }

    protected function processReplacements(string $blade): string
    {
        $map = [

            // unescape
            '/@@(break|continue|foreach|verbatim|endverbatim)/i' => '@$1',

            # remove blade comments
            '/{{--[\s\S]*?--}}/i' => '',

            # echo with a default
            '/{{\s*(.+?)\s+or\s+(.+?)\s*}}/i' => '<?php echo (isset($1)) ? \Phico\Blade\e($1) : $2; ?>',

            # echo an escaped variable, ignoring @{{ var }} for js frameworks
            '/(?<![@]){{\s*(.*?)\s*}}/i' => '<?php echo \Phico\Blade\e($1); ?>',
            # output for js frameworks
            '/@{{\s*(.*?)\s*}}/i' => '{{ $1 }}',

            # echo an unescaped variable
            '/{!!\s*(.+?)\s*!!}/i' => '<?php echo $1; ?>',

            # variable display mutators, wrap these in e() escape function as necessary
            '/@csrf(?![\s(])/i' => '<input type="hidden" name="_csrf_token" value="$csrf">',
            '/@csrf\(([^()]+),\s*["\']([^"\']+)["\']\)/i' => '<input type="hidden" name="$2" value="$1">',
            '/@csrf\s?\((.*?)\)/i' => '<input type="hidden" name="_csrf_token" value="$1">',
            '/@json\((.*?)\s?,\s?(.*?)\s?,\s?(.*?)\s?\)/i' => '<?php echo json_encode($1, $2, $3); ?>',
            '/@json\((.*?)\s?,\s?(.*?)\s?\)/i' => '<?php echo json_encode($1, $2, 512); ?>',
            '/@json\((.*?)\s?\)/i' => '<?php echo json_encode($1, 15, 512); ?>',
            '/@js\((.*?)\s?,\s?(.*?)\s?,\s?(.*?)\s?\)/i' => '<?php echo \Phico\Blade\js($1, $2, $3); ?>',
            '/@js\((.*?)\s?,\s?(.*?)\s?\)/i' => '<?php echo \Phico\Blade\js($1, $2); ?>',
            '/@js\((.*?)\s?\)/i' => '<?php echo \Phico\Blade\js($1); ?>',
            '/@method\s?\((.*?)\)/i' => '<input type="hidden" name="_METHOD" value="$1">',
            '/@method\s?\((.*?)\s?,\s?(.*?)\s?\)/i' => '<input type="hidden" name="$2" value="$1">',
            '/@lower\s?\((.*?)\)/i' => '<?php echo \Phico\Blade\e(strtolower($1)); ?>',
            '/@upper\s?\((.*?)\)/i' => '<?php echo \Phico\Blade\e(strtoupper($1)); ?>',
            '/@ucfirst\s?\((.*?)\)/i' => '<?php echo \Phico\Blade\e(ucfirst(strtolower($1))); ?>',
            '/@ucwords\s?\((.*?)\)/i' => '<?php echo \Phico\Blade\e(ucwords(strtolower($1))); ?>',
            '/@(format|sprintf)\s?\((.*?)\)/i' => '<?php echo \Phico\Blade\e(sprintf($2)); ?>',

            # wordwrap has multiple parameters
            '/@wrap\s?\((.*?)\)/i' => '<?php echo \Phico\Blade\e(wordwrap($1)); ?>',
            '/@wrap\s?\((.*?)\s*,\s*(.*?)\)/i' => '<?php echo \Phico\Blade\e(wordwrap($1, $2)); ?>',
            '/@wrap\s?\((.*?)\s*,\s*(.*?)\s*,\s*(.*?)\)/i' => '<?php echo \Phico\Blade\e(wordwrap($1, $2, $3)); ?>',

            # set and unset statements
            '/@set\(([\'"])(\w+)\1,\s*([\'"])(.*?)\3\)/i' => '<?php $\2 = "\4"; ?>',
            '/@unset\s?\(\$(\w+)\)/i' => '<?php unset($\1); ?>',

            # isset statement
            '/@isset\s?\((.*?)\)/i' => '<?php if (isset($1)): ?>',
            '/@endisset/i' => '<?php endif; ?>',

            # has statement
            '/@has\s?\((.*?)\)/i' => '<?php if (isset($1) && ! empty($1)): ?>',
            '/@endhas/i' => '<?php endif; ?>',

            # handle special unless statement
            '/@unless\s?\((.*?)\)(\s+?)/i' => '<?php if (!($1)): ?>$2',
            '/@endunless/i' => '<?php endif; ?>',

            # special empty statement
            '/@empty\s?\((.*?)\)/i' => '<?php if (empty($1)): ?>',
            '/@endempty/i' => '<?php endif; ?>',

            # switch statement
            '/@switch\s*\((.*?)\)\s*@case\s*\((.*?)\)/s' => "<?php switch($1):\ncase ($2): ?>",
            '/@case\((.*?)\)/i' => '<?php case ($1): ?>',
            '/@default/i' => '<?php default: ?>',
            '/@continue\(\s*(.*)\s*\)/i' => '<?php if($1): continue; endif; ?>',
            '/@continue/i' => '<?php continue; ?>',
            '/@break\(\s*([0-9])\s*\)/i' => '<?php break $1; ?>',
            '/@break\(\s*(.*)\s*\)/i' => '<?php if($1) break; ?>',
            '/@break/i' => '<?php break; ?>',
            '/@endswitch/i' => '<?php endswitch; ?>',

            # handle loops and control structures
            '/@foreach\s?\( *(.*?) *as *(.*?) *\)/i' => '<?php $loop = \Phico\Blade\loop(count($1), $loop ?? null); foreach($1 as $2): ?>',
            '/@endforeach\s*/i' => '<?php $loop->increment(); endforeach; $loop = $loop->parent(); ?>',

            # handle special forelse loop
            '/@forelse\s?\(\s*(\S*)\s*as\s*(\S*)\s*\)(\s*)/i' => "<?php if(!empty($1)): \$loop = \Phico\Blade\loop(count($1), \$loop ?? null); foreach($1 as $2): ?>\n",
            '/@empty(?![\s(])/' => "<?php \$loop->increment(); endforeach; \$loop = \$loop->parent(); ?>\n<?php else: ?>",
            '/@endforelse/' => '<?php endif; ?>',

            // this comes last so it does not match others above
            '/@for\s?(\(.+\s*=\s*(\d+);\s*.+;\s*.+\s*\))/i' => '<?php $loop = \Phico\Blade\loop(\\2, $loop ?? null); for\\1: ?>',
            '/@endfor/' => '<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>',

            # each statements
            # eachelse matches first
            '/@each\s?\((.*)\s*,\s*(.*)\s*,\s*[(\'|\")](.*)[(\'|\")],\s*(.*)\s*\)/i' => "<?php if (!empty($2)): \$loop = \Phico\Blade\loop(count($2), \$loop ?? null); foreach($2 as \$$3): ?>\n@include($1)\n<?php \$loop->increment(); endforeach; \$loop = \$loop->parent(); ?>\n<?php else: ?>\n@include($4)\n<?php endif; ?>",
            '/@each\s?\((.*)\s*,\s*(.*)\s*,\s*[(\'|\")](.*)[(\'|\")]\s*\)/i' => "<?php \$loop = \Phico\Blade\loop(count($2), \$loop ?? null); foreach($2 as \$$3): ?>\n@include($1)\n<?php \$loop->increment(); endforeach; \$loop = \$loop->parent(); ?>",

            // while
            '/@while\s?\((.*)\)/i' => '<?php $loop = \Phico\Blade\loop(count($1), $loop ?? null); while ($1): ?>',
            '/@endwhile/' => '<?php $loop->increment(); endwhile; $loop = $loop->parent(); ?>',

            # control structures
            '/@(if|elseif)\s?\((.*)\)/i' => '<?php $1 ($2): ?>',
            '/@else/i' => '<?php else: ?>',
            '/@endif/' => '<?php endif; ?>',

            # swap out @php and @endphp
            '/@php\((.*)\)/i' => '<?php ($1); ?>',
            '/@php/i' => '<?php',
            '/@endphp/i' => '; ?>',

        ];

        return preg_replace(array_keys($map), array_values($map), $blade);
    }

    protected function compile(array $data = [])
    {
        $this->content = $this->extractVerbatim($this->content);
        $this->content = $this->processDirectives($this->content);

        ob_start();
        extract($data, EXTR_OVERWRITE);
        eval ('?>' . $this->content);
        $this->content = ob_get_clean();
    }

}
