<?php

class MusicSearchService {
  private $api;

  public function dir() {
    print(getcwd());
  }

  /**
   * @param $name
   * @param $url
   * @return void
   */
  public function getImage($name, $url) {
    $date = new DateTime();
    $year = $date->format('Y');
    $month = $date->format('m');

    $img = "sites/default/files/{$year}-{$month}/{$name}cover.jpg";

    // Function to write image into file
    file_put_contents($img, file_get_contents($url));

    echo "File downloaded!";
  }
}
