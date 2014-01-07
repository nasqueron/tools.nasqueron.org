<?php
$palettes = [
    [
        "title" => "Nike box",
        "colors" => [
            ["E33005", "Deep orange"],
            ["333333", "Dark gray"],
            ["F9F6EF", "Near white"],
            ["000000", "Black"],
            ["808080", "Middle gray"]
        ]
    ]
];

require('ase.php');
$content = AdobeSwatchExchangeFile::getASEContent($palettes);
file_put_contents("/tmp/nikebox.ase", $content);
