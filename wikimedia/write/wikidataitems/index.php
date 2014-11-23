<?php
    function get_wikidata_item ($project, $article) {
        $article = str_replace(' ', '_', trim($article));
        $url = "http://www.wikidata.org/w/api.php?action=wbgetentities&sites=enwiki&titles=$article&languages=$project&props=info&format=json";
        $data = json_decode(file_get_contents($url), true);
        return array_pop($data['entities'])['title'];
    }

    $fields = ['list', 'project', 'article'];
    foreach ($fields as $field) {
        $$field = array_key_exists($field, $_POST) ? $_POST[$field] : '';
    }
    if ($list != '') {
        if (!preg_match('/^[A-Za-z\-]+$/', $project)) {
            echo '<div class="alert-box alert">Invalid project code. Should be something like <em>fr</em>, <em>de</em> or <em>en</em>. <a href="" class="close">&times;</a></div>';
        } else {
            $articles = explode("\n", $list);
            $items = [];
            foreach ($articles as $article) {
                $items[] = get_wikidata_item($project, $article);
            }
            $list_items = join("\n", $items);
        }
    }
?>
<dl class="tabs">
  <dd><a href="#Items-article">From a Wikipedia article</a></dd>
  <dd class="active"><a href="#Items-links">From a manual list of Wikipedia articles</a></dd>
</dl>

<ul class="tabs-content">
  <li class="active" id="Items-articleTab">
    <p style="padding-bottom: 1em;">This tool allows you to get Wikidata items from Wikipedia links. It will fetch automatically the links from the text of the specified article.</p>
    <form name="lists" method="post"  class="custom">
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Article</span>
        </div>
        <div class="five mobile-three columns">
            <input
                   name="project" id="project" type="text"
                   placeholder="The language code of the Wikipedia (e.g. en)"
                   value="<?= $project ?>"
            />
                    </div>
        <div class="five mobile-three columns">
            <input
                   name="article" id="article" type="text"
                   placeholder="The article name"
                   value="<?= $article ?>"
            />
        </div>
        <div class="one mobile-one columns">
            <input type="submit" class="button expand postfix" value="Get links" />
        </div>
    </div>
    </form>
  </li>
  <li id="Items-linksTab">
    <p style="padding-bottom: 1em;">This tool allows you to get Wikidata items from Wikipedia links. Write the links and it will fetch the matching wikidata items.</p>
    <form name="lists" method="post"  class="custom">
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Project</span>
        </div>
        <div class="ten mobile-six columns">
            <input
                   name="project" id="project" type="text"
                   placeholder="The language code of the Wikipedia (e.g. en)"
                   value="<?= $project ?>"
            />
        </div>
        <div class="one mobile-one columns">
            <input type="submit" class="button expand postfix" value="Get links" />
        </div>
    </div>
    <div class="row collapse">
        <div class="six columns">
            <textarea name="list" rows="16" style="width: 99%;" placeholder="The Wikipedia article titles"><?= $list ?></textarea>
        </div>
        <div class="six columns">
            <textarea name="items" rows="16" style="width: 99%;" placeholder="The Wikidate items will appear here."><?= $list_items ?></textarea>
       </div>
    </div>
    </form>
  </li>
</ul>
