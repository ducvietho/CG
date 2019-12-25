<?php


namespace App\Traits;


trait ProcessTextSearch
{
    protected function fullText($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);
        $words = explode(' ', $term);
        if(sizeof($words)>1){
            $fulltext = '%';
            foreach($words as $key => $word) {
                /*
                 * applying + operator (required word) only big words
                 * because smaller ones are not indexed by mysql
                 */
                $fulltext = $fulltext.$word.'%';
            }
        }else{
            $fulltext = '%'.$term.'%';
        }
        return $fulltext;
    }
}