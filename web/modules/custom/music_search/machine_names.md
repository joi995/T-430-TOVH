# Content types:

### Album:
    Label: Album Cover, Machine name: field_album_cover, Field type: (Entity reference, Reference type: Media, Media type: Image) - [Required]
    Label: Artist(s), Machine name: field_artist_s, Field type: (Entity reference, Reference type: Content, Content type: Artist) - [Required]
    Label: Description, Machine name: body, Field type: (Text (formatted, long, with summary)) - [Required]
    Label: Front Page Highlight, Machine name: field_front_page_highlight, Field type: (Boolean) - [Not Required]
    Label: Music Category, Machine name: field_music_category, Field type: (Entity reference, Reference type: Taxonomy term, Vocabulary: Types of music) - [Required]
    Label: Publisher, Machine name: field_publisher, Field type: (Entity reference, Reference type: Content, Content type: Publisher) - [Required]
    Label: Release Date, Machine name: field_utgafu_ar, Field type: (Date) - [Required]
    Label: Song(s), Machine name: field_song_s, Field type: (Entity reference, Reference type: Content, Content type: Song) - [Required]

### Artist:
    Label: Artist Type, Machine name: field_artist_type, Field type: (List (text))  - [Required]
    Label: Date of Birth/Creation, Machine name: field_date_of_birth, Field type: (Date)  - [Required]
    Label: Date of Death/Disbanding, Machine name: field_date_of_death, Field type: (Date) - [Not Required]
    Label: Description, Machine name: field_description, Field type: (Text (plain, long)) - [Required]
    Label: Link to Website, Machine name: field_link_to_website, Field type: (Link) - [Required]
    Label: Members, Machine name: field_members, Field type: (Entity reference, Reference type: Content, Content type: Artist) - [Required]
    Label: Picture, Machine name: field_picture, Field type: (Entity reference, Reference type: Media, Media type: Image) - [Required]

### Publisher: (If used)
    Label: Date of Closure, Machine name: field_date_of_closure, Field type: (Date) - [Not Required]
    Label: Date of Creation, Machine name: field_date_of_creation, Field type: (Date) - [Required]
    Label: Description, Machine name: field_description, Field type: (Text (plain, long)) - [Required]
    Label: Link to Website, Machine name: field_link_to_website, Field type: (Link) - [Required]
    Label: Logo, Machine name: field_logo, Field type: (Entity reference, Reference type: Media, Media type: Image) - [Required]
    Label: Picture, Machine name: field_picture, Field type: (Entity reference, Reference type: Media, Media type: Image) - [Not Required]

### Song:
    Label: Music Category, Machine name: field_music_category, Field type: (Entity reference, Reference type: Taxonomy term, Vocabulary: Types of music) - [Required]
    Label: Song length, Machine name: field_song_length, Field type: (Text (plain)) - [Required]
    Label: Song on YouTube, Machine name: field_song_on_youtube, Field type: (Video Embed) - [Required]
    Label: Spotify Embedded, Machine name: field_spotify_embedded, Field type: (Text (formatted) at least 500 characters limit) - [Required]
    Label: Spotify ID, Machine name: field_spotify_id, Field type: (Text (plain)) - [Required]

## Changing Machine Names
The machine names are only used in MusicSearchService.php found at: \
*web/modules/custom/music_search/src/MusicSearchService.php* \
If you want to use different machine names then you just need to go in to \
that file and find our original machine names written above and change them out.
