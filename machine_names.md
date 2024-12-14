Content types:

Album: 
    Label: Album Cover, Machine name: field_album_cover, Field type: (Entity reference, Reference type: Media, Media type: Image) 
    Label: Artist(s), Machine name: field_artist_s, Field type: (Entity reference, Reference type: Content, Content type: Artist) 
    Label: Description, Machine name: body, Field type: (Text (formatted, long, with summary))
    Label: Front Page Highlight, Machine name: field_front_page_highlight, Field type: (Boolean) 
    Label: Music Category, Machine name: field_music_category, Field type: (Entity reference, Reference type: Taxonomy term, Vocabulary: Types of music) 
    Label: Publisher, Machine name: field_publisher, Field type: (Entity reference, Reference type: Content, Content type: Publisher) 
    Label: Release Date, Machine name: field_utgafu_ar, Field type: (Date) 
    Label: Song(s), Machine name: field_song_s, Field type: (Entity reference, Reference type: Content, Content type: Song)

Artist: 
    Label: Artist Type, Machine name: field_artist_type, Field type: (List (text)) 
    Label: Date of Birth/Creation, Machine name: field_date_of_birth, Field type: (Date) 
    Label: Date of Death/Disbanding, Machine name: field_date_of_death, Field type: (Date) 
    Label: Description, Machine name: field_description, Field type: (Text (plain, long)) 
    Label: Link to Website, Machine name: field_link_to_website, Field type: (Link) 
    Label: Members, Machine name: field_members, Field type: (Entity reference, Reference type: Content, Content type: Artist)
    Label: Picture, Machine name: field_picture, Field type: (Entity reference, Reference type: Media, Media type: Image)

Publisher: 
    Label: Date of Closure, Machine name: field_date_of_closure, Field type: (Date) 
    Label: Date of Creation, Machine name: field_date_of_creation, Field type: (Date) 
    Label: Description, Machine name: field_description, Field type: (Text (plain, long)) 
    Label: Link to Website, Machine name: field_link_to_website, Field type: (Link) 
    Label: Logo, Machine name: field_logo, Field type: (Entity reference, Reference type: Media, Media type: Image) 
    Label: Picture, Machine name: field_picture, Field type: (Entity reference, Reference type: Media, Media type: Image)

Song: 
    Label: Music Category, Machine name: field_music_category, Field type: (Entity reference, Reference type: Taxonomy term, Vocabulary: Types of music) 
    Label: Song length, Machine name: field_song_length, Field type: (Text (plain)) 
    Label: Song on YouTube, Machine name: field_song_on_youtube, Field type: (Video Embed) 
    Label: Spotify Embedded, Machine name: field_spotify_embedded, Field type: (Text (formatted) at least 500 characters limit) 
    Label: Spotify ID, Machine name: field_spotify_id, Field type: (Text (plain))
