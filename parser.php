<?php
 $path="C:/Users/1/Desktop/WEB/parsing/";
 $filelist = glob($path . "*.htm");
 for ($j=0; $j < count($filelist) ; $j++)
 {
   $str = file_get_contents ($filelist[$j]);
   //заголовок
   $fn=substr($filelist[$j], 0, -3) . "txt";
   preg_match_all("|g-h1.*?>(.*?)<|s", $str, $out, PREG_SET_ORDER);
   echo $out[0][1] . "<br>";
   file_put_contents ($fn, "Название:" . "\r\n");
   file_put_contents ($fn, str_replace("&nbsp;", " ",trim($out[0][1])) . "\r\n", FILE_APPEND);
   file_put_contents ($fn, "" . "\r\n", FILE_APPEND);
   //продукты
   file_put_contents ($fn, "Ингридиенты:" . "\r\n", FILE_APPEND);
   preg_match_all("|ject.*?: \"(.*?)\"|", $str, $out, PREG_SET_ORDER);
   preg_match_all("|ject.*?t\": \"(.*?)\"|", $str, $out1, PREG_SET_ORDER);
   for ($i=0; $i < count($out)-1; $i++) 
   {
     echo $out[$i][1] . "<br>";
     echo $out1[$i][1] . "<br>";
     file_put_contents ($fn, str_replace("&nbsp;", " ",$out[$i][1]) . "\r\n", FILE_APPEND);
     file_put_contents ($fn, str_replace("&frac12;", "0.5", $out1[$i][1]) . "\r\n", FILE_APPEND);
   }
   file_put_contents ($fn, "" . "\r\n", FILE_APPEND);
   //рецепт
   file_put_contents ($fn, "Рецепт:" . "\r\n", FILE_APPEND);
   preg_match_all("|'i.*?_d.*?n>(.*?)<|", $str, $out, PREG_SET_ORDER);
   preg_match_all("|'i.*?_d.*?/.*?>(.*?)<|", $str, $out1, PREG_SET_ORDER);
   for ($i=0; $i < count($out); $i++) 
   {
     echo $out[$i][1];
     echo $out1[$i][1] . "<br>";
     file_put_contents ($fn, $out[$i][1], FILE_APPEND);
     file_put_contents ($fn, str_replace("&nbsp;", " ",$out1[$i][1]) . "\r\n", FILE_APPEND);
   }
 }
?>
<!-- json -->
