<?php
require_once('datasources/pol.fr.senat.php');
require_once('datasources/pol.fr.assembleenationale.php');

//Gets list of Sénateurs and Députés.
$ds = new SenateursFrance();
$ds->load();
$senateurs = $ds->get_senateurs();

$ds = new AssembleeNationale();
$ds->load();
$deputes = $ds->get_members();

/**
 * Gets a count of items, grouped a key.
 *
 * This achieves the same result than SQL queries like:
 * SELECT foo, COUNT(foo) AS c FROM quux GROUP BY foo ORDER BY c DESC;
 *
 * @param $array an array, each line being an array
 * @param $by the grouping key
 * @return $array an array, each line having the key value as key, and the count as value
 */
function count_by ($array, $by) {
	$count = [];

	foreach ($array as $row) {
		$key = $row[$by];
		if (isset($count[$key])) {
			$count[$key]++;
		} else {
			$count[$key] = 1;
		}
	}
	arsort($count, SORT_NUMERIC);

	return $count;
}

/**
 * Gets a group of items' property, grouped by a key
 *
 * @param $array an array, each line being an array
 * @param $by the grouping key
 *  @return $array an array, each element an array of properties matching the key.
 */
function group_by($array, $by, $property) {
	$groups = [];

	foreach ($array as $row) {
		$key = $row[$by];
		$groups[$key][] = $row[$property];
	}
	ksort($groups, SORT_NUMERIC);

	return $groups;
}
// HTML output
?>
<div class="eight columns">
<h2>Sénat</h2>
<div id="chart-senat"></div>

<h2>Assemblée nationale</h2>
<div id="chart-an"></div>

<h2>Parlement de la République française</h2>
<div id="chart"></div>
</div>
<div class="four columns">
<h2>À propos ...</h2>
<p>Cet outil offre une vue interactive des élus du parlement français, par année de naissance.</p>
<h3>Sources</h3>
<p>La liste des élus et leurs années de naissance proviennent de la <a href="http://fr.wikipedia.org/">Wikipédia francophone</a>.</p>
<h3>SVG</h3>
<ul id="svg"></ul>
<h3>Licence des graphiques</h3>
<p style="text-align: justify"><a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/InteractiveResource" property="dct:title" rel="dct:type">Distribution des parlementaires français par année de naissance</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://www.dereckson.be/" property="cc:attributionName" rel="cc:attributionURL">Sébastien Santoro aka Dereckson</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>.</p>
</div>
<div id="svgtest"></div>
<script>
var senateurs = <?= json_encode(group_by($senateurs, 'born', 'name')); ?>;

var deputes = <?= json_encode(group_by($deputes, 'born', 'name')); ?>;

var serieSenateurs = {
	name: "Sénat",
	data: [
<?php
$count = count_by($senateurs, 'born');
$data = [];
foreach ($count as $key => $value) {
	$data[] = "[$key, $value]";
}
echo implode(', ', $data);
?>
	]
};

var serieDeputes = {
	name: "Ass. nat.",
	data: [
<?php
$count = count_by($deputes, 'born');
$data = [];
foreach ($count as $key => $value) {
	$data[] = "[$key, $value]";
}
echo implode(', ', $data);
?>
	]
};

var chartParams = {
    title: {
        text: "Distribution des parlementaires français par année de naissance."
    },
    legend: {
        visible: true
    },
    series: [ serieSenateurs, serieDeputes ],
    seriesDefaults: {
        type: "scatter"
    },
    seriesColors: [
            "#ff6800",
            "#C77966",
        ],
    xAxis: {
	min: 1920,
        max: 1990,
        labels: {
            format: "{0}"
        },
        title: {
            text: "Année de naissance"
        }
    },
    yAxis: {
        max: 30,
        labels: {
            format: "{0}"
        },
        title: {
            text: "Nombre d'élus"
        }
    },
    tooltip: {
        visible: true,
	template: function(chart) {
		var value = chart.value;
		if (chart.series.name == "Sénat") {
			var title = "sénateur";
			var members = senateurs[value.x];
		} else {
			var title = "député";
			var members = deputes[value.x];
		}
		var text = value.y + " " + title + s(value.y) + " né" + s(value.y) + " en " + value.x + " : <ul class='tooltipList";
		if (members.length > 20) {
			text += " block-grid four-up xlarger";
		} else if (members.length > 10) {
			text += " block-grid two-up larger";
		}
		text += "'>";
		for (var i = 0 ; i < members.length ; i++) {
			text += "<li>" + members[i] + "</li>";
		}
		text += "</ul>";
		return text;
	},

    }
};
$("#chart").kendoChart(chartParams);

var chartSenatParams = jQuery.extend(true, {}, chartParams);
chartSenatParams.title.text = "Distribution des sénateurs français par année de naissance.";
chartSenatParams.series = [ serieSenateurs ];
$("#chart-senat").kendoChart(chartSenatParams);

var chartANParams = jQuery.extend(true, {}, chartParams);
chartANParams.title.text = "Distribution des députés français par année de naissance.";
chartANParams.series = [ serieDeputes ];
$("#chart-an").kendoChart(chartANParams);

// Save to SVG

function base64_encode (data) {
  // http://phpjs.org/functions/base64_encode/
  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
    ac = 0,
    enc = "",
    tmp_arr = [];

  if (!data) {
    return data;
  }

  do { // pack three octets into four hexets
    o1 = data.charCodeAt(i++);
    o2 = data.charCodeAt(i++);
    o3 = data.charCodeAt(i++);

    bits = o1 << 16 | o2 << 8 | o3;

    h1 = bits >> 18 & 0x3f;
    h2 = bits >> 12 & 0x3f;
    h3 = bits >> 6 & 0x3f;
    h4 = bits & 0x3f;

    // use hexets to index into b64, and append result to encoded string
    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
  } while (i < data.length);

  enc = tmp_arr.join('');

  var r = data.length % 3;

  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
}

function get_svg (id) {
	var content = $(id).data("kendoChart").svg();
	return base64_encode(content.replace("?xml version='1.0'", "?xml version='1.0' encoding='iso-8859-1'"));
}

document.getElementById("svg").innerHTML =
	'<li><a href="data:image/svg+xml;base64,' + get_svg("#chart-senat") + '">Le Sénat</a></li>' +
	'<li><a href="data:image/svg+xml;base64,' + get_svg("#chart-an") + '">' + "L'Assemblée nationale</a></li>" +
	'<li><a href="data:image/svg+xml;base64,' + get_svg("#chart") + '">Les deux chambres</a></li>';
</script>
