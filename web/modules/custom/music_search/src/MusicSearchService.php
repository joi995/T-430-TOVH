<?php

namespace Drupal\music_search;

use Drupal\node\Entity\Node;

class MusicSearchService {
  private $api;

  /**
   *
   *
   * @param string $name
   * @param string $url
   * @return void
   */
  public function getImage($name, $url) {
    $date = new \DateTime();
    $year = $date->format('Y');
    $month = $date->format('m');

    $img = DRUPAL_ROOT . "/web/sites/default/files/{$year}-{$month}/{$name}cover.jpg";

    // Function to write image into file
    file_put_contents($img, file_get_contents($url));

    echo "File downloaded!";
  }

  /**
   * Save album data to a node.
   *
   * @param array $data
   *   The album data.
   *
   * @return int
   *   The ID of the saved album node.
   */
  public function saveAlbum(array $data) {
    // Create the album node directly using provided data.
    $node = Node::create([
      'type' => 'album',
      'title' => $data['title'],
      'field_artist_s' => $data['artist'], // Multiple artist IDs accepted as an array.
      'field_utgafu_ar' => $data['release_date'], // Release date in proper format ('Y-m-d').
      'body' => $data['description'], // Album description (formatted text).
      'field_publisher' => $data['publisher'] ?? null, // Publisher node ID.
      'field_song_s' => $data['songs'] ?? [], // Array of Song node IDs.
      'field_music_category' => $data['music_category'] ?? null, // Taxonomy term reference for music category.
    ]);

    // Save the node and return its ID.
    $node->save();
    return $node->id();
  }

  /*
   * Example usage:
   *
   * $data = [
   *   'title' => 'Greatest Hits',
   *   'artist' => [1, 2], // Array of Artist node IDs.
   *   'release_date' => '2023-11-01', // Release date in proper format ('Y-m-d').
   *   'description' => 'A special collection of hit songs.', // Album description.
   *   'publisher' => 5, // Single Publisher node ID.
   *   'songs' => [10, 11, 12], // Array of Song node IDs.
   *   'music_category' => 7, // Single taxonomy term ID for music category.
   * ];
   */

  /**
   * Save artist data to a node.
   *
   * @param array $data
   *   The artist data.
   *
   * @return int
   *   The ID of the saved artist node.
   */
  public function saveArtist(array $data) {
    $node = Node::create([
      'type' => 'artist',
      'title' => $data['name'], // Use title as the artist name.
      'field_artist_type' => $data['artist_type'], // List (text) - e.g., "Band", "Solo".
      'field_date_of_birth' => $data['birth_date'] ?? null, // Date for birth/creation.
      'field_date_of_death' => $data['death_date'] ?? null, // Date for death/disbanding.
      'field_description' => $data['description'] ?? null, // Plain text description.
      'field_link_to_website' => $data['website'] ?? null, // A link to the artist's website.
      'field_members' => $data['members'] ?? [], // Entity reference to other artist nodes (e.g., band members).
      'field_picture' => $data['picture'] ?? null, // Entity reference to a Media entity (image).
    ]);

    $node->save();
    return $node->id();
  }

  /*
   * Example usage:
   *
   * $data = [
   *   'name' => 'Epic Band',
   *   'artist_type' => 'Band',
   *   'birth_date' => '2000-01-01',
   *   'death_date' => null,
   *   'description' => 'A description about the band.',
   *   'website' => 'https://example.com',
   *   'members' => [10, 11], // Array of other artist node IDs (e.g., member references).
   *   'picture' => 25, // Media entity ID for the image.
   * ];
   */

  /**
   * Save publisher data to a node.
   *
   * @param array $data
   *   The publisher data.
   *
   * @return int
   *   The ID of the saved publisher node.
   */
  public function savePublisher(array $data) {
    $node = Node::create([
      'type' => 'publisher',
      'title' => $data['name'], // Use title as the publisher name.
      'field_date_of_creation' => $data['creation_date'] ?? null, // Date for publisher creation.
      'field_date_of_closure' => $data['closure_date'] ?? null, // Date for publisher closure.
      'field_description' => $data['description'] ?? null, // Plain long text for description.
      'field_link_to_website' => $data['website'] ?? null, // Link to the publisher's website.
      'field_logo' => $data['logo'] ?? null, // Entity reference to Media (logo image).
      'field_picture' => $data['picture'] ?? null, // Entity reference to Media (general image).
    ]);

    $node->save();
    return $node->id();
  }

  /*
   * Example usage:
   *
   * $data = [
   *   'name' => 'Amazing Music Publishing',
   *   'creation_date' => '1990-06-15',
   *   'closure_date' => null,
   *   'description' => 'A leading publisher of amazing music worldwide.',
   *   'website' => 'https://amazing-music-publishing.com',
   *   'logo' => 30, // Media entity ID for logo image.
   *   'picture' => 31, // Media entity ID for general picture.
   * ];
   */

  /**
   * Save song data to a node.
   *
   * @param array $data
   *   The song data.
   *
   * @return int
   *   The ID of the saved song node.
   */
  public function saveSong(array $data) {
    $node = Node::create([
      'type' => 'song',
      'title' => $data['title'], // Use title as the song name.
      'field_music_category' => $data['music_category'] ?? null, // Taxonomy term for music category.
      'field_song_length' => $data['song_length'] ?? null, // Song length (plain text; e.g., "3:45").
      'field_song_on_youtube' => $data['youtube_url'] ?? null, // YouTube video embed URL.
      'field_spotify_embedded' => $data['spotify_embedded'] ?? null, // Formatted text for Spotify embed.
      'field_spotify_id' => $data['spotify_id'] ?? null, // Plain text for Spotify song ID.
    ]);

    $node->save();
    return $node->id();
  }

  /*
   * Example usage:
   *
   * $data = [
   *   'title' => 'Epic Symphony',
   *   'music_category' => 8, // Taxonomy term ID for Music category.
   *   'song_length' => '5:12', // Song length (example: "5:12").
   *   'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
   *   'spotify_embedded' => '<iframe>Spotify Embed Code</iframe>',
   *   'spotify_id' => '123456789',
   * ];
   */

}
