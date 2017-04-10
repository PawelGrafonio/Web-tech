<?php
	include 'simple_html_dom.php';
	$html = file_get_html('https://eda.ru/recepty/vypechka-deserty/cvetaevskij-jablochnij-pirog-15574');
	if(count($html->find('h1.recipe__name')))
		foreach($html->find('h1.recipe__name') as $head1){
			$result1=$head1->innertext;
			echo $result1;
			echo "</br>";
		}
	if(count($html->find('span.js-tooltip-ingredient')))
		foreach($html->find('span.js-tooltip-ingredient') as $ing){
			$result2=$ing->innertext;
			echo $result2;
			echo "</br>";
		}
	if(count($html->find('span[class=ingredients-item__measure js-ingredient-measure-amount]')))
		foreach($html->find('span[class=ingredients-item__measure js-ingredient-measure-amount]') as $kol){
			$result3=$kol->innertext;
			echo $result3;
			echo "</br>";
		}
	if(count($html->find('span.instruction__description')))
		foreach($html->find('span.instruction__description') as $des){
			$result4=$des->innertext;
			echo $result4;
			echo "</br>";
		}
	$html->clear();
	unset($html);
?>