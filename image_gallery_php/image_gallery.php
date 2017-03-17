<?php

class ImageGallery {
  var $page = 1;
  var $per_page = 5;
  var $tags = '';

  function __construct(){
    // get tags params from get
    if (isset($_GET['tags']))
      $this->tags = $_GET['tags'];
    else
      $this->tags = '';

    // get page params from get
    if (isset($_GET['page']))
      $this->page = $_GET['page'];
    else
      $this->page = 1;
  }


  /**
   * Generate paginator
   */
  function showPaginator(){
    $current_page = $_SERVER["PHP_SELF"];
    $params = '';
    if ($this->tags != ''){
      $params = '&tags=' . $this->tags;
    }
    echo "<li id='prev_page'><a href='$thispage?page=" . (intval($this->page)-1) . $params . "' >Prev</a></li>\n";
    echo "<li id='next_page'><a href='$thispage?page=" . (intval($this->page)+1) . $params . "' >Next</a></li>\n";
  }


  /**
   * Get the images from flickr using their API and show them
   */
  function showImages(){
    // Params to use in get request
    if ($this->tags == ''){
      $method = 'flickr.photos.getRecent';
    } else{
      $method = 'flickr.photos.search';
    }

    $params = array(
      'api_key'   => bc00f9225ed46fbfe08befc774193029,
      'method'    => $method,
      'per_page'  => $this->per_page,
      'page'      => $this->page,
      'format'    => 'json',
      'nojsoncallback' => 1,
      'tags' => $this->tags
    );
    try {
    // building api url with params
      $flikr_api_url = "https://api.flickr.com/services/rest/?".http_build_query($params);
      // get json data from flickr api
      $json = file_get_contents($flikr_api_url);
      $photos = json_decode($json, true)['photos'];

      for ($i = 0; $i < count($photos['photo']); $i++) {
        // for every element create an image
        $thumburl = $this->getImageURL($photos['photo'][$i], "t");
        $imgurl = $this->getImageURL($photos['photo'][$i], "");
        echo "<div class='col-lg-2 col-md-2 col-xs-4 thumb'>";
        echo "  <a href=\"$imgurl\" target='_blank'><img src=\"$thumburl\" style='padding: 3px;'></a>";
        echo "</div>";
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  /**
   * build image src url from json got it from flickr
   *
   * @param (json) $photoAr json from flickr
   * @param (string) $suffix indicate image size
   *                  t	thumbnail, 100 on longest side
   *                  m	small, 240 on longest side
   * @return (url) flickr image src url
   */
  function getImageURL($photoAr, $suffix = "") {
    if ($suffix != "")
      $suffix = "_" . $suffix;
    $url = "http://farm" . $photoAr['farm'] . ".static.flickr.com/";
    $url .= $photoAr['server'] . "/" . $photoAr['id'] . "_" . $photoAr['secret'];
    $url .= $suffix.".jpg";
    return $url;
  }
}
?>
