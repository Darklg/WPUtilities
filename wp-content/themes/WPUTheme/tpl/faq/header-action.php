<?php
/**
 * Actions for FAQ
 *
 * @package default
 */


$content_faq = '';
ob_start();
the_content();
$content_raw = ob_get_clean();
$content = explode('<h3>', $content_raw);

foreach ($content as $faq_element) {
    if (!empty($faq_element)) {
        $faq_element = str_replace('</h3>', '</h3><div class="faq-element__content">', $faq_element);
        $content_faq .= '<div class="faq-element"><h3 class="faq-element__title">'.$faq_element.'</div></div>';
    }
}

