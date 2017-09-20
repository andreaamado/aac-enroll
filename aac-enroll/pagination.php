<?php
function pagination($parameters, $array) {
    if ($array != 0) :
        $qtd = 30;
        $pagination = array();
        $pages = array_chunk($array, $qtd);
        $pageCurrent = intval($parameters['aac_page']);
        $pageOpen = (($pageCurrent * $qtd) - $qtd);

        for ($h = 1; $h < count($pages) + 1; $h++) {
            if ($h == $pageCurrent) {
                $pagination[$h]['pag'] = "[ $h ] ";
            } else {
                $pagination[$h]['pag'] = "[ <a href=\"" . $parameters['url'] . "&type=" . $parameters['type'] . "&aac_page=$h\">$h</a> ] ";
            }
        }

        $pagination[0]['current'] = $pageOpen;
        $pagination[0]['qtd'] = $qtd;
        return $pagination;
    endif;
}
?>