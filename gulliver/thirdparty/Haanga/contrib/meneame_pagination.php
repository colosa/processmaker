<?php

class Haanga_Extension_Tag_MeneamePagination
{
    public $is_block = FALSE;

    static function generator($cmp, $args, $redirected)
    {
        if (count($args) != 3 && count($args) != 4) {
            throw new Haanga_CompilerException("Memeame_Pagination requires 3 or 4 parameters");
        }

        if (count($args) == 3) {
            $args[3] = 5;
        }

        $current = hvar('mnm_current');
        $total   = hvar('mnm_total');
        $start   = hvar('mnm_start');
        $end     = hvar('mnm_end');
        $prev    = hvar('mnm_prev');
        $next    = hvar('mnm_next');
        $pages   = 'mnm_pages';
        
        $code = hcode();
        
        $code->decl($current, $args[0]);
        $code->decl($total, hexec('ceil', hexpr($args[2], '/', $args[1])) );
        $code->decl($start, hexec('max', hexpr($current, '-', hexec('intval', hexpr($args[3],'/', 2))), 1));
        $code->decl($end, hexpr($start, '+', $args[3], '-', 1));
        $code->decl($prev, hexpr_cond( hexpr(1, '==', $current), FALSE, hexpr($current, '-', 1)) ); 
        $code->decl($next, hexpr_cond( hexpr($args[2], '<', 0, '||', $current, '<', $total), hexpr($current, '+', 1), FALSE));
        $code->decl('mnm_pages', hexec('range', $start, hexpr_cond(hexpr($end,'<', $total), $end, $total)));

        $cmp->set_safe($current);
        $cmp->set_safe($total);
        $cmp->set_safe($prev);
        $cmp->set_safe($next);
        $cmp->set_safe($pages);
        

        return $code;
    }

}
