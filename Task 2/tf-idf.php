<?php
ini_set ('memory_limit', '1024M');
$dictionary = array();
$folder_name = "corpus";
$num_files = count(glob("$folder_name/*.txt"));

foreach (glob("$folder_name/*.txt") as $filename) {

    $text = file_get_contents($filename, "r");
    $text = strtolower(preg_replace("/\n\r|\n|\r/"," ", $text));

    preg_match_all('/[a-zA-Z-]+/',$text,$token);
    foreach ($token[0] as $word) {
      if (array_key_exists($word, $dictionary))
      {
        if (!array_key_exists($filename, $dictionary[$word]))
        {
          $dictionary[$word][$filename] = 1;
        }
        else {
          $dictionary[$word][$filename] += 1;
        }
      }
      else
      {
        $dictionary[$word][$filename] = 1;
      }
    }
}

ksort($dictionary);   //nach keywords sortiert

do {
    $query = strtolower(readline("Enter query, leave empty to quit: "));
    $query_array = explode(" ", $query);
    $result = array();
    if ($query !="") {
    foreach (glob("$folder_name/*.txt") as $filename) {
        foreach ($query_array as $queryword) {
          if (array_key_exists($queryword, $dictionary) && array_key_exists($filename, $dictionary[$queryword])){
            $idf = $num_files/count($dictionary[$queryword]);
            $tf = $dictionary[$queryword][$filename];

            $result[$filename][$queryword] = $idf * $tf;

            if (!array_key_exists("scrnr", $result[$filename])) {
              $result[$filename]["scrnr"]=$result[$filename][$queryword];
            }
            else $result[$filename]["scrnr"]+= $result[$filename][$queryword];
          }
        }
      }
      if (count($result)>1) {

      uasort($result, function ($file1, $file2) {
        return $file2['scrnr'] <=> $file1['scrnr'];
      });
    }
      if (count($result) > 5) {
        $top = "top 5";
      }
      elseif (count($result) == 0) {
        $top = "none, because THERE ARE NONE";
      }
      else {
        $top = "all of them";
      }

      echo "\nFound ".count($result)." results, showing $top \n";
      $limit = 0;
      foreach ($result as $file => $value) {
        $file = basename($file);
        echo "\n$file: score: $value[scrnr] \n";
        foreach ($value as $key => $count) {
          if ($key != "scrnr") {
            echo "$key: $count\n";
          }
        }
        if (++$limit == 5) break;     //max 5
      }
    }
    echo "\n";
} while ($query);

?>
