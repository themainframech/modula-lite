<script type="text/html" id="tmpl-modula-single-image">
    <div class="modula-single-image" data-id="{{ data.id }}">
        <div class="modula-single-image-content">
            <# 
            if ( data.image != '' ) { 
                #>
                <img src="{{ data.image }}" alt="{{ data.alt }}" />
                <# 
            } 
            #>
            <div class="actions">
                <a href="#" class="modula-edit-image""><span class="dashicons dashicons-edit"></span></a>
                <a href="#" class="modula-delete-image""><span class="dashicons dashicons-trash"></span></a>
            </div>
            <div class="values">
                <input type="hidden" name="modula-images[id][]" class="modula-image-id" value="{{ data.id }}">
                <input type="hidden" name="modula-images[alt][]" class="modula-image-alt" value="{{ data.alt }}">
                <input type="hidden" name="modula-images[title][]" class="modula-image-title" value="{{ data.title }}">
                <input type="hidden" name="modula-images[caption][]" class="modula-image-caption" value="{{ data.caption }}">
                <input type="hidden" name="modula-images[halign][]" class="modula-image-halign" value="{{ data.halign }}">
                <input type="hidden" name="modula-images[valign][]" class="modula-image-valign" value="{{ data.valign }}">
                <input type="hidden" name="modula-images[link][]" class="modula-image-link" value="{{ data.link }}">
                <input type="hidden" name="modula-images[target][]" class="modula-image-target" value="{{ data.target }}">
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-modula-image-editor">
    <div class="edit-media-header">
        <button class="left dashicons"><span class="screen-reader-text"><?php _e( 'Edit previous media item', 'modula-gallery' ); ?></span></button>
        <button class="right dashicons"><span class="screen-reader-text"><?php _e( 'Edit next media item', 'modula-gallery' ); ?></span></button>
    </div>
    <div class="media-frame-title">
        <h1><?php _e( 'Edit Metadata', 'modula-gallery' ); ?></h1>
    </div>
    <div class="media-frame-content">
        <div class="attachment-details save-ready">
            <!-- Left -->
            <div class="attachment-media-view portrait">
                <div class="thumbnail thumbnail-image">
                    <img class="details-image" src="{{ data.src }}" draggable="false" />
                </div>
            </div>
            
            <!-- Right -->
            <div class="attachment-info">
                <!-- Settings -->
                <div class="settings">
                    <!-- Attachment ID -->
                    <input type="hidden" name="id" value="{{ data.id }}" />
                    
                    <!-- Image Title -->
                    <label class="setting">
                        <span class="name"><?php _e( 'Title', 'modula-gallery' ); ?></span>
                        <input type="text" name="title" value="{{ data.title }}" />
                        <div class="description">
                            <?php _e( 'Image titles can take any type of HTML. You can adjust the position of the titles in the main Lightbox settings.', 'modula-gallery' ); ?>
                        </div>
                    </label>
                  
                    
                    <!-- Alt Text -->
                    <label class="setting">
                        <span class="name"><?php _e( 'Alt Text', 'modula-gallery' ); ?></span>
                        <input type="text" name="alt" value="{{ data.alt }}" />
                        <div class="description">
                            <?php _e( 'Very important for SEO, the Alt Text describes the image.', 'modula-gallery' ); ?>
                        </div>
                    </label>

                    <!-- Caption Text -->
                    <label class="setting">
                        <span class="name"><?php _e( 'Caption Text', 'modula-gallery' ); ?></span>
                        <textarea name="caption">{{ data.caption }}</textarea>
                        <div class="description">
                        </div>
                    </label>

                    <!-- Alignment -->
                    <div class="setting">
                        <span class="name">Alignment</span>
                        <select name="halign" class="inline-input">
                            <option <# if ( 'left' == data.halign ) { #> selected <# } #>>left</option>
                            <option <# if ( 'center' == data.halign ) { #> selected <# } #>>center</option>
                            <option <# if ( 'right' == data.halign ) { #> selected <# } #>>right</option>
                        </select>
                        <select name="valign" class="inline-input">
                            <option <# if ( 'top' == data.valign ) { #> selected <# } #>>top</option>
                            <option <# if ( 'middle' == data.valign ) { #> selected <# } #>>middle</option>
                            <option <# if ( 'bottom' == data.valign ) { #> selected <# } #>>bottom</option>
                        </select>
                    </div>
                    
                    <!-- Link -->
                    <div class="setting">
                        <label class="">
                            <span class="name"><?php _e( 'URL', 'modula-gallery' ); ?></span>
                            <input type="text" name="link" value="{{ data.link }}" />
                            <span class="description">
                                <?php _e( 'Enter a hyperlink if you wish to link this image to somewhere other than its full size image.', 'modula-gallery' ); ?>
                            </span>
                        </label>
                        <label>
                        <span class="description">
                            <input type="checkbox" name="target" value="1"<# if ( data.target == '1' ) { #> checked <# } #> />
                            <?php _e( 'Opens your image links in a new browser window / tab.', 'modula-gallery' ); ?>
                        </span>
                        </label>
                    </div>
                    

                    <!-- Addons can populate the UI here -->
                    <div class="modula-addons"></div>
                </div>
                <!-- /.settings -->     
               
                <!-- Actions -->
                <div class="actions">
                    <a href="#" class="modula-gallery-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', 'modula-gallery' ); ?>">
                        <?php _e( 'Save Metadata', 'modula-gallery' ); ?>
                    </a>

                    <!-- Save Spinner -->
                    <span class="settings-save-status">
                        <span class="spinner"></span>
                        <span class="saved"><?php _e( 'Saved.', 'modula-gallery' ); ?></span>
                    </span>
                </div>
                <!-- /.actions -->
            </div>
        </div>
    </div>
</script>