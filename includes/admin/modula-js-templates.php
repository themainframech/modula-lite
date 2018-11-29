<script type="text/html" id="tmpl-modula-image">
    <div class="modula-single-image-content" <# if ( data.full != '' ) { #> style="background-image:url({{ data.full }})" <# } #> >
        <# if ( data.thumbnail != '' ) { #>
            <img src="{{ data.thumbnail }}">
        <# } #>
        <div class="actions">
            <a href="#" class="modula-edit-image""><span class="dashicons dashicons-edit"></span></a>
            <a href="#" class="modula-delete-image""><span class="dashicons dashicons-trash"></span></a>
        </div>
        <div class="values">
            <input type="hidden" name="modula-images[id][{{data.index}}]" class="modula-image-id" value="{{ data.id }}">
            <input type="hidden" name="modula-images[alt][{{data.index}}]" class="modula-image-alt" value="{{ data.alt }}">
            <input type="hidden" name="modula-images[title][{{data.index}}]" class="modula-image-title" value="{{ data.title }}">
            <input type="hidden" name="modula-images[description][{{data.index}}]" class="modula-image-description" value="{{ data.description }}">
            <input type="hidden" name="modula-images[halign][{{data.index}}]" class="modula-image-halign" value="{{ data.halign }}">
            <input type="hidden" name="modula-images[valign][{{data.index}}]" class="modula-image-valign" value="{{ data.valign }}">
            <input type="hidden" name="modula-images[link][{{data.index}}]" class="modula-image-link" value="{{ data.link }}">
            <input type="hidden" name="modula-images[target][{{data.index}}]" class="modula-image-target" value="{{ data.target }}">
            <input type="hidden" name="modula-images[width][{{data.index}}]" class="modula-image-width" value="{{ data.width }}">
            <input type="hidden" name="modula-images[height][{{data.index}}]" class="modula-image-height" value="{{ data.height }}">
            <?php do_action( 'modula_item_extra_fields' ) ?>
        </div>
        <div class="segrip ui-resizable-handle ui-resizable-se"></div>
    </div>
</script>

<script type="text/html" id="tmpl-modula-image-editor">
    <div class="edit-media-header">
        <button class="left dashicons"><span class="screen-reader-text"><?php esc_html_e( 'Edit previous media item', 'modula-gallery' ); ?></span></button>
        <button class="right dashicons"><span class="screen-reader-text"><?php esc_html_e( 'Edit next media item', 'modula-gallery' ); ?></span></button>
    </div>
    <div class="media-frame-title">
        <h1><?php _e( 'Edit Metadata', 'modula-gallery' ); ?></h1>
    </div>
    <div class="media-frame-content">
        <div class="attachment-details save-ready">
            <!-- Left -->
            <div class="attachment-media-view portrait">
                <div class="thumbnail thumbnail-image">
                    <img class="details-image" src="{{ data.full }}" draggable="false" />
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
                        <span class="name"><?php esc_html_e( 'Title', 'modula-gallery' ); ?></span>
                        <input type="text" name="title" value="{{ data.title }}" />
                        <div class="description">
                            <?php _e( 'Image titles can take any type of HTML. You can adjust the position of the titles in the main Lightbox settings.', 'modula-gallery' ); ?>
                        </div>
                    </label>
                  
                    
                    <!-- Alt Text -->
                    <label class="setting">
                        <span class="name"><?php esc_html_e( 'Alt Text', 'modula-gallery' ); ?></span>
                        <input type="text" name="alt" value="{{ data.alt }}" />
                        <div class="description">
                            <?php _e( 'Very important for SEO, the Alt Text describes the image.', 'modula-gallery' ); ?>
                        </div>
                    </label>

                    <!-- Caption Text -->
                    <label class="setting">
                        <span class="name"><?php esc_html_e( 'Caption Text', 'modula-gallery' ); ?></span>
                        <textarea name="description">{{ data.description }}</textarea>
                        <div class="description">
                        </div>
                    </label>

                    <!-- Alignment -->
                    <div class="setting">
                        <span class="name"><?php esc_html_e( 'Alignment', 'modula-gallery' ); ?></span>
                        <select name="halign" class="inline-input">
                            <option <# if ( 'left' == data.halign ) { #> selected <# } #>><?php esc_html_e( 'left', 'modula-gallery' ); ?></option>
                            <option <# if ( 'center' == data.halign ) { #> selected <# } #>><?php esc_html_e( 'center', 'modula-gallery' ); ?></option>
                            <option <# if ( 'right' == data.halign ) { #> selected <# } #>><?php esc_html_e( 'right', 'modula-gallery' ); ?></option>
                        </select>
                        <select name="valign" class="inline-input">
                            <option <# if ( 'top' == data.valign ) { #> selected <# } #>><?php esc_html_e( 'top', 'modula-gallery' ); ?></option>
                            <option <# if ( 'middle' == data.valign ) { #> selected <# } #>><?php esc_html_e( 'middle', 'modula-gallery' ); ?></option>
                            <option <# if ( 'bottom' == data.valign ) { #> selected <# } #>><?php esc_html_e( 'bottom', 'modula-gallery' ); ?></option>
                        </select>
                    </div>
                    
                    <!-- Link -->
                    <div class="setting">
                        <label class="">
                            <span class="name"><?php esc_html_e( 'URL', 'modula-gallery' ); ?></span>
                            <input type="text" name="link" value="{{ data.link }}" />
                            <span class="description">
                                <?php _e( 'Enter a hyperlink if you wish to link this image to somewhere other than its attachment page. In order to use it you will need to select attachment page on Lightbox & Links setting under General.', 'modula-gallery' ); ?>
                            </span>
                        </label>
                        <label>
                        <span class="description">
                            <input type="checkbox" name="target" value="1"<# if ( data.target == '1' ) { #> checked <# } #> />
                            <span><?php esc_html_e( 'Opens your image links in a new browser window / tab.', 'modula-gallery' ); ?></span>
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
                        <?php esc_html_e( 'Save Metadata', 'modula-gallery' ); ?>
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