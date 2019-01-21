<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Задание 1 </title>
    <link rel="stylesheet" href="bower_components/chartist/dist/chartist.min.css">
    <script src="bower_components/chartist/dist/chartist.min.js"></script>
  </head>
  <body style="width:1000px; margin:0 auto;">
  <h2 style="text-align:center">Задание 1 </h2>
  <hr>
  <b>Текущий язык:</b>
  <form name="form1">
  <input type="radio" name="lang" id="ru" value="ru" <?php if($_GET['lang']=='ru') echo "checked=\"checked\""; ?>><label for="ru">Русский</label></a>
  <input type="radio" name="lang" id="eng" value="eng" <?php if($_GET['lang']=='eng') echo "checked=\"checked\""; ?>><label for="eng">Английский</label></a>
  <input type="radio" name="lang" id="by" value="by" <?php if($_GET['lang']=='by') echo "checked=\"checked\""; ?>><label for="by">Белорусский</label></a>  
  <input type="hidden" name="driver" id="file" value="file" <?php if($_GET['driver']=='file') echo "checked=\"checked\""; ?>><label for="file"></label></a>  
  <a href="#" class="show" onclick="return go();">Показать</a>
  </form>
  <hr>
<?php
$lang = $_GET['lang'];
$data =   file_get_contents('files/'.$lang.'.txt');
echo $rgreg;
include 'File.php';
$file = new File();
$data = $file->getData();

  
$data = Zipf::full_trim($data); // обрезание лишних пробелов

// разбиение слов на массивы
$in = mb_strtolower($data, 'UTF-8');
$in = preg_replace("'ё'u", "е", $in);
//$arrWorks = preg_match_all("'[a-zа-яё]+'u", $in, $m) ? $m[0] : array();
$arrWorks = explode(' ',$in);
foreach($arrWorks as $key => $value){
	if($value=="") unset($arrWorks[$key]);
}
$data_count = count($arrWorks); // количество слов в тексте
$collection = Zipf::createObjParap($arrWorks); // нахождение частоты
$collection = Zipf::p($collection,$data_count); // нахождение вероятности
$collection = Zipf::sorting($collection); // сортировка по количеству входжений слов в тексте
$collection = Zipf::rank($collection); // находим (расчитывыем) ранг
$collection = Zipf::c($collection,$data_count); // нахождение c 
$collectionRes = Zipf::for_print($collection,$data_count);
$collectionRes2 = Zipf::for_print2($collection,$data_count);
?>
<table id="res">
<tbody>
  <tr>
  <td  width="450"><!--Описание-->
    <div style="height:500px; overflow: auto;">
    <table>
    <tbody>
      <tr>
      <td>Формы слова</td>
      <td>Частота повторений</td>
      <td>Вероятность</td>
     <!-- <td>Константа С</td>-->
      <td>Ранг</td>
      </tr>
  <?php
    foreach($collection as $key => $value){
      echo "<tr>";
      echo "<td>".$value->work."</td>";
      echo "<td>".$value->frequency."</td>";
      echo "<td>".$value->p."</td>";
     // echo "<td>".$value->c."</td>";
     echo "<td>".$value->rank."</td>";
      echo "</tr>";
    }
  ?>
  </tbody>
  </table>
  </div>
  </td>
  <td>
	<?php
	echo "<p id=\"zipf_conts\"><b>Константа Зипа С:</b>  ".round(Zipf::c_total($collection,$data_count),3)."</p>";
	echo "<p><b>Количество слов в тексте:</b> ".$data_count."</p>";
	echo "<p><b>Количество уникальных слов:</b> ".count($collection)."</p>";
	$collection = Zipf::StopWorks($collection);
	echo "<p><b>Список стоп-слов:</b> ".$collection[1] . "</p>"; // стоп слова
	$collection = Zipf::q($collection[0]);
	$collection = Zipf::k($collection);
	echo "<p><b>Список ключевых слов:</b> ".Zipf::getKeys($collection) . "</p>"; // ключи
	?>
  </td>
</tr>
</tbody>
</table>
<!---->
<script>
function go(){
  var lang = form1.lang.value;
  var driver = form1.driver.value;
  location.href = 'index.php?lang=' + lang;
}
</script>
<br><br>

<table>
<tr>
	<td width="545px">
		<div style="text-align:left">Частота вхождения слова в текст</div>
		<div class="ct-chart ct-golden-section" style="text-align:center ">
		<script>
		new Chartist.Line('.ct-chart', {
		labels: [
			<?php foreach($collectionRes as $key => $value){ echo "'$value',"; } ?>
		],
		series: [
			[
			<?php foreach($collectionRes as $key => $value){ echo "'$key',"; } ?>
			]
		]}, {
		low: 0,
		showArea: true,
		// не отрисовывать точки линейного графика
		showPoint: false,
		fullWidth: true,  
		// Настройки X-оси
		axisX: {
			// Отключаем сетку для этой оси
			showGrid: true,
			// и не показываем метки
			showLabel:true
		},
		// настройки Y-оси 
		axisY: {
			// Смещение от меток
			offset: 60,
			// Функция интерполяции метки позволяет менять значение метки,
			// в текущем примере появляются миллионы "m".
			labelInterpolationFnc: function(value) {
			return  value.toFixed(3);
			}
		}
		});
		
		</script>
		</div>
		<div style="text-align:right">Ранг слова в списке</div>
	</td>
	<td width="455px">
		<div style="text-align:left;">Количество слов</div>
		<div class="ct-chart2 ct-perfect-fourth">
		<script>
		new Chartist.Line('.ct-chart2', {
		labels: [
			<?php foreach($collectionRes2 as $key => $value){ echo "'$key',"; } ?>
		],
		series: [
			[
			<?php foreach($collectionRes2 as $key => $value){ echo "'$value',"; } ?>
			]
		]}, {
		low: 0,
		showArea: true,
		// не отрисовывать точки линейного графика
		showPoint: false,
		fullWidth: true,  
		// Настройки X-оси
		axisX: {
			// Отключаем сетку для этой оси
			showGrid: true,
			// и не показываем метки
			showLabel:true
		},
		});
		</script>
		</div>
		<div style="text-align:right">Частота</div>
	</td>
</tr>
</table>

<hr>
<p style="text-align:center "> &copy 2018, Равкович С.В. гр.581074</p>



<?php
class Zipf {
	public $work; // само слово
	public $frequency; // частота(количество)
	public $p; // вероятность вхождения
	public $c; // коэфициент зипфа
	public $rank; // ранг
	public $q; // лямда
	public $k; // вес термина
	
	public function __construct($str){
		$this->work = $str;
		$this->frequency = 1;
		$this->p = 1;
		$this->c = 1;
		$this->rank = 1;
		$this->q = 0;
		$this->k = 0;
		
	}
	
	// удаление всех лишних пробелов
	public static function full_trim($str){
		$str = str_replace('.', ' ', $str);
		$str = str_replace(',', ' ', $str);
		$str = str_replace('*', '', $str);
		$str = str_replace(';', '', $str);
		$str = str_replace('—', '', $str);
		$str = str_replace('–', '', $str);
		$str = str_replace('„', '', $str);
		$str = str_replace(':', '', $str);
		$str = str_replace('»', '', $str);
		$str = str_replace('«', '', $str);  
		$str = str_replace('?', '', $str);
		$str = str_replace('!', '', $str); 
		$str = str_replace('[', '', $str);
		$str = str_replace(']', '', $str); 
		$str = str_replace('…', '', $str);  
		$str = str_replace('-', '', $str); 
		$str = str_replace('    ', ' ', $str); 
		$str = str_replace('   ', ' ', $str); 
		$str = str_replace('  ', ' ', $str); 
		$str = str_replace('1', '', $str);
		$str = str_replace('2', '', $str);
		$str = str_replace('3', '', $str);
		$str = str_replace('4', '', $str);
		$str = str_replace('5', '', $str);
		$str = str_replace('6', '', $str);
		$str = str_replace('7', '', $str);
		$str = str_replace('8', '', $str);
		$str = str_replace('9', '', $str);  
		$str = str_replace('0', '', $str);  
		// $str = mb_strtolower($str);
		// $str = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","",$str);
		//$str = trim(preg_replace('/\s{2,}/', ' ', $str));     
		
		return $str;
	}
	
	// находжение частоты
	public static function createObjParap($arr){
		$resDB = array();
		foreach($arr as $key => $value){
			$b = false;
			$pos = 0;
			foreach($resDB as $key2 => $value2){
				if($value==$value2->work) {
					$b = true;
					$pos = $key2;
					break;
				}
			}
			if(!$b){
				$obj = new self($value);
				$resDB [] = $obj;
			}
			else{
				$resDB [$pos]->frequency++;
			}
		}
		return $resDB;
	}
	
	// сортировка по количеству входжений слов в тексте
	public static function sorting($arr){
		$i=0;
		while ($i<(count($arr)-1)){
			for($j=0; $j<=(count($arr)-1); $j++){
				if($arr[$j]->frequency<$arr[$i]->frequency){
					$temp = $arr[$j];
					$arr[$j] = $arr[$i];
					$arr[$i] = $temp;
				}
			}
			$i++;
		}
		return $arr;
	}
	
	// нахождение вероятности входжения
	public static function p($arr,$count){
		foreach($arr as $key => $value){
			$value->p = number_format($value->frequency / $count, 3);
		}
		return $arr;
	}
	
	// нахождение C
	public static function c($arr,$count){
		foreach($arr as $key => $value){		
			$value->c = $value->p * $value->rank ;
		}
		return $arr;
	}
	
	// нахождение C общего
	public static function c_total($arr,$count){
		$sum = 0;
		$i =0;
		foreach($arr as $key => $value){
			if($i!=$value->rank){
				$sum =$sum + $value->c;
				$i++;
			}
			else continue;
		}		
		return $sum  / $arr[count($arr)-1]->rank;
	}
	
		// подсчет ранга
	public static function rank($arr){
		$r = 1;
		$count = $arr[0]->frequency;
		foreach($arr as $key => $value){
			if($value->frequency!=$count) {
				$count = $value->frequency;
				$r++;
				$value->rank = $r;
			} else{
				$value->rank = $r;
			}
		}
		return $arr;
	}
	
	public static function for_print($arr,$count){
		$res = array();
		foreach($arr as $key => $value){			
			$res[$value->p] = $value->rank;			
		}		
		return $res;
	}
	
	public static function for_print2($arr,$count){
		$res = array();
		$temp = array();
		$temp2 = array();
		
		for($i=0; $i<count($arr); $i++){
			$temp [$arr[$i]->frequency] = $arr[$i]->frequency;
		}
		ksort($temp);
		$keys = array_keys($temp);
		$temp2 = array_reverse($temp, true);
		$keys2 = array_keys($temp2);		
		$symm = 0;		
		foreach ($keys2 as $key => $value) {	
			$symm = 0;		
			for($i=0; $i<count($arr); $i++){
				if ($arr[$i]->frequency == $keys[$key]){
					$symm++;
				}else continue;
			}	
			$res[$keys[$key]] = $symm;
		}	
	return $res;
	}
	
	public static function StopWorks($arr){
		$res = array();
		//include 'stop-words.php';
		
		$stopWorks = array('а','б','в','г','д','е','ё','ж','з','и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
		'для', 'на', 'по', 'со', 'из', 'от', 'до', 'без', 'над', 'под', 'за', 'при', 'после', 'во','не', 'же', 'то', 'бы', 'всего', 'итого', 'даже', 'да', 'нет',
		'или', 'но', 'дабы', 'затем', 'потом', 'коли', 'лишь только', 'как', 'так', 'еще', 'тот', 'откуда', 'зачем', 'почему', 'он', 'мы', 'его', 'вы', 'вам', 'вас', 'ее', 'что',
		'который','которая','которых','которое', 'их', 'все', 'они', 'я', 'весь', 'мне', 'меня', 'таким', 'весь', 'всех', 'кб', 'мб', 'дн', 'руб', 'ул', 'кв', 'дн', 'гг','ой', 
		'ого', 'эх', 'что-то', 'какой-то', 'где-то', 'как-то', 'зачем-то', 'из-за', 'дальше', 'ближе', 'раньше', 'позже', 'когда-то','себя','её','перед','тем','себе','она','еще',
		'ещё','это', 'например', 'на самом деле', 'однако', 'вообще', 'в общем', 'всего', 'почти', 'примерно', 'около', 'где-то', 'порядка', 'ему', 'только','сам', 'чтото','опять', 
		'нему',  'раз', 'него', 'ни', 
		
		"about", "above", "according",  "across", "actually", "ad", "adj", "ae", "af", "after",  "afterwards", "ag", "again", "against", "ai", "al", "all", "almost", "alone",
		"along", "already", "also", "although", "always", "am", "among", "amongst", "an", "and", "another", "any", "anyhow", "anyone", "anything", "anywhere", "ao", "aq",
		"ar", "are", "aren", "aren't", "around", "arpa", "as", "at", "au", "aw", "bb", "bd", "be", "became", "because", "become", "becomes", "becoming", 
		"been", "before", "beforehand", "begin", "beginning",  "behind", "being", "below", "beside", "besides", "between", "beyond", "bf", "bg", "bh", "bi",
		"billion", "bj", "bm", "bn", "bo", "both", "br", "bs", "bt", "but", "buy", "bv", "bw", "by",  "can", "can't", "cannot", "caption", "cc", "cd", "cf",
		"eg", "ch", "ci", "ck", "cl", "click", "cm", "cn", "со", "со.", "com", "copy", "could", "couldn", "couldn't", "cr", "cs", "cu", "cv", "cx", "cy", "did", "didn", "didn't",
		"dj", "dk", "dm", "do", "does", "doesn", "doesn't", "don", "don't", "down", "during", "ее", "edu", "eg", "eh", "eight", 
		"eighty",  "either", "else", "elsewhere", "end", "ending", "enough", "er", "es", "et", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", 
		"fi", "fifty", "find", "first", "five", "fj", "fk", "fm",  "fo", "for", "former", "formerly", "forty", "found", "four", "fr", "free", 
		"from", "further", "gb", "gd", "ge", "get", "gf", "gg", "gh", "gi", "gl", "gm", "gmt", "gn", "go", "gov", "gp", "gq", "gr", "gs", "gt", "gu",
		"gw", "has", "hasn", "hasn't", "have", "haven", "haven't", "he", "he'd", "he'll", "he's", "help", "hence", "her", "here", "here's", "hereafter", "hereby",
		"herein", "hereupon", "hers", "herself", "him", "himself", "his", "hk", "hm", "hn", "home", "homepage", "how", "however", "hr", "ht", "htm", "html", "http",
		"hu", "i", "i'll", "i'm", "i've", "id", "ie", "if", "ii", "il", "im", "in", "inc", "inc.", "indeed", "information", "instead", "int", "into", "io", "iq", "ir", "is",
		"isn", "isn't", "it", "it's", "its", "itself","last", "later", "latter", "lb", "Ic", "least", "less", "let", "let's", "li", "like", "likely", "Ik", "ll",
		"Ir", "Is", "It", "ltd", "lu", "Iv",  "made", "make", "makes", "many", "maybe", "mc", "md", "me", "meantime", "meanwhile", "mg", "mh", "microsoft", "might",
		"mil", "million", "miss", "mk", "ml", "mm", "itin", "mo", "more", "moreover", "most", "mostly", "mp", "mq", "mr", "mrs", "ms", "msie", "mt", "mu", "much", "must", "mv",
		"mw", "mx", "my", "myself", "namely", "nc", "ne", "neither", "net", "netscape", "never",  "nevertheless", "new", "next", "nf", "ng", "ni", "nine", "ninety", "nl",
		"no", "nobody", "none", "nonetheless", "noone", "nor", "not", "nothing", "now", "nowhere", "np", "nr", "nu", "null", "off", "often", "om", "on", 
		"once", "one's", "only", "onto", "or", "org", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "overall", "page", "ре",
		"perhaps", "pf", "pg", "ph", "pk", "pi", "pm", "pn", "pr", "pt", "pw", "re", "recent", "recently", "reserved", "ring", "ro", "ru", "same", "sb", "sc", "sd",
		"se", "seem", "seemed", "seeming", "seems", "seven", "seventy", "several", "sg", "sh", "she", "she'd", "she'll", "she's", "should", "shouldn", "shouldn't", "si", "since",
		"site", "six", "sixty", "sj", "sk", "si", "sm", "sn", "so", "some","somehow", "someone", "something", "sometime", "sometimes", "somewhere", "sr", "st", "still", "stop", "su", 
		"such", "sv", "sy", "tc", "td", "ten", "text", "tf", "tg", "test", "th", "than", "that", "that'll", "that's", "the", "their",  "them", "themselves", "then", "thence", 
		"there", "there'll", "there's", "thereafter", "thereby", "therefore", "therein", "thereupon","these", "they", "they'd", "they'll", "they're", "they've", "thirty", "this", 
		"those", "though", "thousand", "three", "through", "throughout", "thru", "thus","tj", "tk", "tm", "tn", "to","together", "too", "toward", "towards", "tp", "tr", "trillion",
		"tt", "tv", "tw", "twenty", "two",  "uk", "um", "under", "unless", "unlike", "unlikely", "until", "up", "upon", "us", "use", "used", "using", "uy","vc", "ve",
		"very", "vg", "vi", "via", "vn", "wasn", "wasn't", "we", "we'd", "we'll", "we're", "we've", "web", "webpage", "website", "welcome", "well", "were", "weren", "weren't",
		"wf", "what", "what'll", "what's", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon","wherever", "whether",
		"which", "while", "whither", "who", "who'd", "who'll", "who's", "whoever", "whole", "whom", "whomever", "whose", "why", "will", "with", "within", "without", 
		"won", "won't", "would", "wouldn", "wouldn't", "ws", "www", "yes", "yet", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself",
		"yourselves", "yt", "zm", "zr",  "of", "a", "was", "had", "one",
		
		"і", "яго", "ў", "што", "ён", "як", "было", "яшчэ", "гэты",  "каб", "дзе", "па",   "але", "яму", "быў",  "хто", "гэта", "ад",  "толькі", "калі",
		"яны","пасля", "яе", "была", "тут", "яна", "можа", "іх", "нават", 
		"мяне","таго","той", "сказаў", "гэтага", "праз",  "таму", "якія", "які", 
		"былі", "пра",  "ты", "хоць", "вельмі", "якая", "я", "бо", "зараз", "два", "нас", "ўсё", "ўсе", "ўжо", "гэтым"
		);
		
		foreach($arr as $key => $value){
			foreach($stopWorks as $key2 => $value2){
					if($value->work==$value2){
					$res [] = $value2;
					unset($arr[$key]);
					break;
				}
			}
		}
		return array($arr,implode(', ',$res));
	}
	
	public static function q($arr){
		foreach($arr as $key => $value){
			$arr[$key]->q = log(3/ 1);
		}
		return $arr;
	}
	
	public static function k($arr){
		foreach($arr as $key => $value){
			$arr[$key]->k = $value->frequency * $value->q;
		}
		return $arr;
	}
	
	public static function getKeys($arr){
		$res = array();
		$i=0;
		foreach($arr as $key => $value){
			$res [] = $value->work;
			$i++;
			if($i==16) break;
		}	
		return implode(', ',$res);
	}
} ?>


<frame width="500">
</frame>
</body>
</html>