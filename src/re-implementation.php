<?php


$r = new Renderer();
echo $r->render('child', [
    'var' => 'child var is set',
    'parent_var' => 'parent var is set',
]);

class Renderer
{
    protected string $extends = '';
    protected array $directives = [];
    protected array $sections = [];
    protected array $section_stack = [];


    protected function load(string $filename): string
    {
        return file_get_contents("$filename.blade.php");
    }
    protected function save($filename, $code)
    {
        static $i;
        $i++;
        file_put_contents("$i-$filename.php", $code);
    }

    public function render(string $filename, array $data = []): string
    {
        $code = $this->load($filename);

        $this->save('1-loaded', $code);

        $code = $this->compile($code);

        $this->save('2-compiled', $code);

        while (!empty($this->extends)) {
            // set parent to the name of template being extended
            $parent = $this->extends;
            $this->save('3-parent', $parent);
            // reset for handling multi-level inheritance
            $this->extends = '';
            // fetch parent content
            $parent = $this->load($parent);
            $this->save('4-parent-loaded', $parent);
            // replace content with parent (parent sections are already saved in stacks)
            $code = $this->compile($parent);
            $this->save('5-parent-compiled', $code);
        }
        $code = $this->swaps($code);
        $code = $this->interpolate($code, $data);
        $this->save('6-interpolated', $code);
        return $code;
    }

    protected function compile(string $code): string
    {
        foreach ($this->directives as $name => $handler) {
            $pattern = "/@{$name}(\((.*?)\))?/";
            $code = preg_replace_callback($pattern, function ($matches) use ($handler) {
                $expression = isset($matches[2]) ? "({$matches[2]})" : '()';
                return call_user_func($handler, $expression);
            }, $code);
        }

        $code = $this->eval($code);

        return $code;
    }

    protected function eval(string $code): string
    {
        ob_start();
        eval ('?>' . $code);
        $code = ob_get_clean();
        return $code;
    }

    protected function interpolate(string $code, array $data = [])
    {
        ob_start();
        extract($data, EXTR_OVERWRITE);
        eval ('?>' . $code);
        $code = ob_get_clean();
        return $code;
    }





    public function __construct()
    {
        $this->directives['section'] = function ($expression) {
            return "<?php \$this->startSection{$expression}; ?>";
        };
        $this->directives['endsection'] = function () {
            return "<?php \$this->endSection(); ?>";
        };
        $this->directives['append'] = function () {
            return "<?php \$this->appendSection(); ?>";
        };
        $this->directives['yield'] = function ($expression) {
            return "<?php echo \$this->yieldSection{$expression}; ?>";
        };
        $this->directives['extends'] = function ($expression) {
            return "<?php \$this->extends{$expression}; ?>";
        };
        $this->directives['show'] = function () {
            return "<?php \$this->showSection(); ?>";
        };
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


    protected function swaps(string $blade): string
    {
        $map = [

            // unescape
            '/@@(break|continue|foreach|verbatim|endverbatim)/i' => '@$1',

            # remove blade comments
            '/{{--[\s\S]*?--}}/i' => '',

            # echo with a default
            '/{{\s*(.+?)\s+or\s+(.+?)\s*}}/i' => '<?php echo (isset($1)) ? e($1) : $2; ?>',

            # echo an escaped variable, ignoring @{{ var }} for js frameworks
            '/(?<![@]){{\s*(.*?)\s*}}/i' => '<?php echo e($1); ?>',
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
            '/@js\((.*?)\s?,\s?(.*?)\s?,\s?(.*?)\s?\)/i' => '<?php echo js($1, $2, $3); ?>',
            '/@js\((.*?)\s?,\s?(.*?)\s?\)/i' => '<?php echo js($1, $2); ?>',
            '/@js\((.*?)\s?\)/i' => '<?php echo js($1); ?>',
            '/@method\s?\((.*?)\)/i' => '<input type="hidden" name="_METHOD" value="$1">',
            '/@method\s?\((.*?)\s?,\s?(.*?)\s?\)/i' => '<input type="hidden" name="$2" value="$1">',
            '/@lower\s?\((.*?)\)/i' => '<?php echo e(strtolower($1)); ?>',
            '/@upper\s?\((.*?)\)/i' => '<?php echo e(strtoupper($1)); ?>',
            '/@ucfirst\s?\((.*?)\)/i' => '<?php echo e(ucfirst(strtolower($1))); ?>',
            '/@ucwords\s?\((.*?)\)/i' => '<?php echo e(ucwords(strtolower($1))); ?>',
            '/@(format|sprintf)\s?\((.*?)\)/i' => '<?php echo e(sprintf($2)); ?>',

            # wordwrap has multiple parameters
            '/@wrap\s?\((.*?)\)/i' => '<?php echo e(wordwrap($1)); ?>',
            '/@wrap\s?\((.*?)\s*,\s*(.*?)\)/i' => '<?php echo e(wordwrap($1, $2)); ?>',
            '/@wrap\s?\((.*?)\s*,\s*(.*?)\s*,\s*(.*?)\)/i' => '<?php echo e(wordwrap($1, $2, $3)); ?>',

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
            '/@foreach\s?\( *(.*?) *as *(.*?) *\)/i' => '<?php $loop = loop(count($1), $loop ?? null); foreach($1 as $2): ?>',
            '/@endforeach\s*/i' => '<?php $loop->increment(); endforeach; $loop = $loop->parent(); ?>',

            # handle special forelse loop
            '/@forelse\s?\(\s*(\S*)\s*as\s*(\S*)\s*\)(\s*)/i' => "<?php if(!empty($1)): \$loop = loop(count($1), \$loop ?? null); foreach($1 as $2): ?>\n",
            '/@empty(?![\s(])/' => "<?php \$loop->increment(); endforeach; \$loop = \$loop->parent(); ?>\n<?php else: ?>",
            '/@endforelse/' => '<?php endif; ?>',

            // this comes last so it does not match others above
            '/@for\s?(\(.+\s*=\s*(\d+);\s*.+;\s*.+\s*\))/i' => '<?php $loop = loop(\\2, $loop ?? null); for\\1: ?>',
            '/@endfor/' => '<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>',

            # each statements
            # eachelse matches first
            '/@each\s?\((.*)\s*,\s*(.*)\s*,\s*[(\'|\")](.*)[(\'|\")],\s*(.*)\s*\)/i' => "<?php if (!empty($2)): \$loop = loop(count($2), \$loop ?? null); foreach($2 as \$$3): ?>\n@include($1)\n<?php \$loop->increment(); endforeach; \$loop = \$loop->parent(); ?>\n<?php else: ?>\n@include($4)\n<?php endif; ?>",
            '/@each\s?\((.*)\s*,\s*(.*)\s*,\s*[(\'|\")](.*)[(\'|\")]\s*\)/i' => "<?php \$loop = loop(count($2), \$loop ?? null); foreach($2 as \$$3): ?>\n@include($1)\n<?php \$loop->increment(); endforeach; \$loop = \$loop->parent(); ?>",

            // while
            '/@while\s?\((.*)\)/i' => '<?php $loop = loop(count($1), $loop ?? null); while ($1): ?>',
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
}

function e(string $str)
{
    return htmlentities($str, ENT_QUOTES);
}
function loop(int $i, $parent = null)
{

}

/*
str = child.content
if str.extends
    this.content = compile(parent.content)			// load parent content as main content, creating sections & stacks




render(file, data):
    str = loadCompiled(file)
    interpolate(str, data)


loadCompiled(file): str
if cache hit
    return load from cache(file) eval it to get structure

str = load from templates
str = compile(str)
cache(file, str)
return str


compile(str):
while str.contains.extends
    content = loadCompiled(parent)

*/
