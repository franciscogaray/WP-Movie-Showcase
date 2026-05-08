import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Button, TextControl, Placeholder, PanelBody, ToggleControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export default function Edit( { attributes, setAttributes } ) {
	const { searchTitle, movie, showPoster }        = attributes;
	const [ inputValue, setInputValue ] = useState( searchTitle || '' );
	const blockProps                    = useBlockProps();

	const handleSearch = async() => {
		const title    = inputValue.trim();
		if ( ! title ) {
			return;
		}

		const result = await apiFetch(
			{
				path: addQueryArgs( '/wp-movie-showcase/v1/search', { title } ),
			}
		);

		setAttributes(
			{
				searchTitle: title,
				movie: result,
			}
		);
	};

	return (
        <>
            <InspectorControls>
                <PanelBody
                    title={ __( 'Display Settings', 'wp-movie-showcase' ) }
                    initialOpen={ true }
                >
                    <ToggleControl
                        label={ __( 'Show poster', 'wp-movie-showcase' ) }
                        help={
                            showPoster
                                ? __( 'Poster is displayed.', 'wp-movie-showcase' )
                                : __( 'Poster is hidden.', 'wp-movie-showcase' )
                        }
                        checked={ showPoster }
                        onChange={ ( value ) =>
                            setAttributes( { showPoster: value } )
                        }
                        __nextHasNoMarginBottom
                    />
                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                <Placeholder
                    icon="video-alt2"
                    label={ __( 'Movie Showcase', 'wp-movie-showcase' ) }
                    instructions={ __( 'Search a movie by title.', 'wp-movie-showcase' ) }
                >
                    <TextControl
                        value={ inputValue }
                        onChange={ setInputValue }
                        placeholder={ __( 'e.g. The Matrix', 'wp-movie-showcase' ) }
                        __nextHasNoMarginBottom
                    />
                    <Button variant="primary" onClick={ handleSearch }>
                        { __( 'Search', 'wp-movie-showcase' ) }
                    </Button>

                    { movie && (
                        <p style={ { marginTop: 12 } }>
                            <strong>{ movie.title }</strong> ({ movie.year })
                            { ' — ' }
                            { showPoster
                                ? __( 'Poster will be shown.', 'wp-movie-showcase' )
                                : __( 'Poster will be hidden.', 'wp-movie-showcase' ) }
                        </p>
                    ) }
                </Placeholder>
            </div>
        </>
	);
}