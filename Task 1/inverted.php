<?php

$dictionary = array();

foreach (glob("ressources/*.txt") as $filename) {
    $text = file_get_contents($filename, "r");
    $text = strtolower(preg_replace("/\n\r|\n|\r/"," ", $text));

    preg_match_all('/[a-zA-Z-]+/',$text,$token);
    foreach ($token[0] as $word) {
      if (array_key_exists($word, $dictionary))
      {
        if (!array_key_exists($filename, $dictionary[$word]))
        {
          array_push($dictionary[$word], $filename);
        }
      }
      else
      {
        $dictionary[$word]=[$filename];
      }
    }
}
ksort($dictionary);

do {
    $query = strtolower(readline("Enter query, leave empty to quit: "));
    $query_array = explode(" ", $query);
    $intersect = array_intersect_key($dictionary, array_flip($query_array));
    if (count($intersect)>1) {
      $result = array_unique(call_user_func_array('array_intersect', $intersect));
    }
    else {
      $result = array_key_exists(reset($query_array), $intersect)? $intersect[reset($query_array)] : array();
    }

    $resultstring = implode(", ", $result);
    echo "$resultstring\n";
} while ($query);
?>
