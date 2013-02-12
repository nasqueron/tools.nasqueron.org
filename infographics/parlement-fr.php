<div class="four columns">
<?php
require_once('datasources/pol.fr.senat.php');

//Gets list of Sénateurs.
$ds = new SenateursFrance();
$ds->load();
$senateurs = $ds->get_senateurs();

/**
 * Gets a count of items, grouped a key.
 *
 * This achieves the same result than SQL queries like:
 * SELECT foo, COUNT(foo) AS c FROM quux GROUP BY foo ORDER BY c DESC;
 *
 * @param $objects an array, each line being an array
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

// HTML output
echo '<h2>Sénateurs</h2><table class="twelve"><thead><tr><th>Nom</th><th>Né en</th></tr></thead><tbody>';
foreach ($senateurs as $senateur) {
	echo "<tr><td><a href='$senateur[URL]'>$senateur[name]</a></td><td>$senateur[born]</td></tr>";
}
echo "</tbody></table></div>";
?>
<div class="eight columns">
<h2>Distribution par année de naissance</h2>
<div id="chart"></div>
<div id="chart-1980"></div>
<p>Les données pour l'Assemblée Nationale ne sont pas encore disponibles.</p>
</div>
<script>
var chartParams = {
    title: {
        text: "Distribution des parlementaires français par année de naissance."
    },
    legend: {
        visible: true
    },
    seriesDefaults: {
        type: "scatter"
    },
    series: [{
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
    }],
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
        format: "{1} sénateurs nés en {0}"
    }
};
$("#chart").kendoChart(chartParams);
chartParams.xAxis.max = 1976;
$("#chart-1980").kendoChart(chartParams);

</script>
