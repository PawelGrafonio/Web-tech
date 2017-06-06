<?php
  //линк для использования mysqli
  $link = mysqli_connect("localhost", "root", "", "site");
  $recepts = mysqli_query ($link, "SELECT name, ingr, rec_text FROM recepts");
  if (mysqli_num_rows($recepts) != 0)
  {
    for ($i=0; $i < mysqli_num_rows($recepts) ; $i++)
    {
      mysqli_data_seek($recepts, $i);
      $row = mysqli_fetch_row($recepts);
      echo "<h3>".$row[0]."</h3><br><b>Ингридиенты:</b> ".$row[1]."<br><b>Рецепт:</b><br>".$row[2]."<br>&nbsp<br>";
    }
  }
?>
