<?php

namespace Drupal\music_search;

use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class MusicSearchService {
  use StringTranslationTrait;
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
    // Initialize variables for Media entity.
    $media_id = null;

    // Check if an image URL ("picture_url") is provided for the album cover.
    if (!empty($data['picture_url'])) {
      try {
        // Download the image and save it as a File entity.
        $file_uri = $this->downloadImage($data['picture_url'], $data['title']);
        $file = File::create([
          'uri' => $file_uri,
          'status' => 1, // Permanent file.
        ]);
        $file->save();

        // Create a Media entity of type Image for the album cover.
        $media = Media::create([
          'bundle' => 'image', // Media type: "image".
          'name' => $data['title'] . ' Cover', // A name for the Media entity.
          'field_media_image' => [
            'target_id' => $file->id(),
            'alt' => $data['title'] . ' cover image',
            'title' => $data['title'] . ' Cover',
          ],
          'status' => 1, // Published.
        ]);
        $media->save();

        // Store the Media ID to be attached to the album node later.
        $media_id = $media->id();
      } catch (\Exception $e) {
        // Handle errors gracefully and log the issue.
        \Drupal::messenger()->addError($this->t('An error occurred while processing the album cover: @error', [
          '@error' => $e->getMessage(),
        ]));
      }
    }

    // Step 3: Create the Album node and attach the Media entity as the album cover.
    $node = Node::create([
      'type' => 'album', // Content type: "Album".
      'title' => $data['title'], // Album title.
      'field_artist_s' => $data['artist'], // Multiple artist IDs accepted as an array.
      'field_utgafu_ar' => $data['release_date'], // Release date in proper format ('Y-m-d').
      'body' => $data['description'], // Album description (formatted text).
      'field_publisher' => $data['publisher'] ?? null, // Publisher node ID.
      'field_song_s' => $data['songs'] ?? [], // Array of Song node IDs.
      'field_music_category' => $data['music_category'] ?? null, // Taxonomy term reference for music category.
      'field_album_cover' => $media_id ? ['target_id' => $media_id] : null, // Attach Media entity as Album Cover.
    ]);

    // Save the node and return its ID.
    $node->save();
    return $node->id();
  }
// Example usage:
//  $data = [
//  'title' => 'Epic Album',
//  'artist' => [1, 2], // Array of Artist node IDs.
//  'release_date' => '2023-11-01', // Release date in proper format ('Y-m-d').
//  'description' => 'This album is a masterpiece of epic proportions.', // Album description.
//  'publisher' => 5, // Publisher node ID.
//  'songs' => [10, 11, 12], // Array of Song node IDs.
//  'music_category' => 8, // Taxonomy term ID for music category.
//  'picture_url' => 'https://example.com/epic_album_cover.jpg', // URL to the album cover image.
//  ];

  /**
   * Save artist data to a node.
   *
   * @param array $data
   *   The artist data.
   *
   * @return int
   *   The ID of the saved artist node.
   */
  public function saveArtist(array $data)
  {
    // Initialize variables for Media entity.
    $media_id = null;

    // Check if an image URL ("picture_url") is provided and handle it.
    if (!empty($data['picture_url'])) {
      try {
        // Download the image and save it as a File entity.
        $file_uri = $this->downloadImage($data['picture_url'], $data['name']);
        $file = File::create([
          'uri' => $file_uri,
          'status' => 1, // Permanent file.
        ]);
        $file->save();

        // Create a Media entity of type Image.
        $media = Media::create([
          'bundle' => 'image', // Media type: "image".
          'name' => $data['name'] . ' Image', // A name for the Media entity.
          'field_media_image' => [
            'target_id' => $file->id(),
            'alt' => $data['name'] . ' cover image',
            'title' => $data['name'] . ' Image',
          ],
          'status' => 1, // Published.
        ]);
        $media->save();

        // Store the Media entity ID to attach it to the Artist node later.
        $media_id = $media->id();
      } catch (\Exception $e) {
        // Handle errors gracefully and log the issue.
        \Drupal::messenger()->addError($this->t('An error occurred while processing the image: @error', [
          '@error' => $e->getMessage(),
        ]));
      }
    }

    // Step 3: Create the Artist node.
    $node = Node::create([
      'type' => 'artist', // Content type: "Artists".
      'title' => $data['name'], // Name of the artist.
      'field_artist_type' => $data['artist_type'], // Artist type (e.g., "Solo", "Band").
      'field_date_of_birth' => $data['birth_date'] ?? null, // Date of birth/creation.
      'field_date_of_death' => $data['death_date'] ?? null, // Date of death/disbanding.
      'field_description' => $data['description'] ?? null, // Plain text description.
      'field_link_to_website' => $data['website'] ?? null, // Spotify URL for the artist.
      'field_members' => $data['members'] ?? [], // Members (other Artist references).
      'field_picture' => $media_id ? ['target_id' => $media_id] : null, // Attach Media entity as Picture.
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
      'field_spotify_embedded' => t('<iframe style="border-radius:12px" src="@uid?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',['@uid' => $data['spotify_embedded']])  ?? null, // Formatted text for Spotify embed.
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


  /**
   * Download an image from a URL and save it in the files directory. (NEW: Function added)
   *
   * @param string $url
   *   The image URL.
   * @param string $name
   *   A name for the image file.
   *
   * @return string
   *   The URI of the saved file.
   */


  public function downloadImage(string $url, string $name): string {
    $date = new \DateTime();
    $year = $date->format('Y');
    $month = $date->format('m');

    // Set the directory to save images.
    $directory = "public://{$year}-{$month}/";

    // Prepare the directory (create it if it doesn't exist).
    \Drupal::service('file_system')->prepareDirectory(
      $directory,
      \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY
    );

    // Define the image filename and path.
    $filename = preg_replace('/[^a-z0-9_\-]/i', '_', $name) . '_picture.jpg';
    $file_uri = $directory . $filename;

    // Download the image and save it locally using the resolved real path.
    try {
      $image_data = file_get_contents($url); // Get file data from the URL.
      if ($image_data === false) {
        throw new \Exception('Unable to download the file.');
      }

      // Use the file system service to resolve the real file path.
      $real_path = \Drupal::service('file_system')->realpath($file_uri);
      file_put_contents($real_path, $image_data);

      // Return the Drupal file URI.
      return $file_uri;
    } catch (\Exception $e) {
      watchdog_exception('music_search', $e);
      throw new \Exception($this->t('Failed to download the image: @message', ['@message' => $e->getMessage()]));
    }
  }

}
