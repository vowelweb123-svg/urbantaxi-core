/**
 * UrbanTaxi Hero Slider — Frontend Script
 *
 * Initialises Swiper for every .ut-hero-slider element on the page.
 * Options are passed via the data-swiper-options attribute (JSON).
 */
( function () {
	'use strict';
	let hooksRegistered = false;

	/**
	 * Initialise a single slider element.
	 *
	 * @param {HTMLElement} el
	 */
	function syncWrapperBackground( sliderEl ) {
		if ( ! sliderEl ) {
			return;
		}

		const wrapper = sliderEl.closest( '.ut-hero-slider-wrapper' );
		if ( ! wrapper ) {
			return;
		}

		const activeSlide = sliderEl.querySelector( '.swiper-slide-active' ) || sliderEl.querySelector( '.swiper-slide' );
		const bgUrl = activeSlide && activeSlide.dataset ? activeSlide.dataset.slideBg : '';

		if ( bgUrl ) {
			wrapper.style.backgroundImage = 'url("' + bgUrl + '")';
		} else {
			wrapper.style.backgroundImage = 'none';
		}
	}

	function initSlider( el ) {
		if ( ! el || el.swiper || el.classList.contains( 'swiper-initialized' ) ) {
			return;
		}

		const wrapper = el.closest( '.ut-hero-slider-wrapper' );

		let options = {};

		try {
			const raw = el.dataset.swiperOptions;
			if ( raw ) {
				options = JSON.parse( raw );
			}
		} catch ( e ) {
			console.warn( 'UrbanTaxi Hero Slider: invalid swiper options JSON', e );
		}

		// Map simplified option flags to full Swiper config objects.
		const config = {
            loop: options.loop || false,
            speed: 500,
            grabCursor: true,
            effect: 'slide',
            // fadeEffect: {
            //  crossFade: true,
            // },
            on: {
                init: function () {
                    syncWrapperBackground( this.el );
                },
                slideChangeTransitionStart: function () {
                    syncWrapperBackground( this.el );
                },
            },
        };

		if ( options.autoplay ) {
			config.autoplay = {
				delay: options.autoplay.delay || 5000,
				disableOnInteraction: false,
			};
		}

		if ( options.navigation ) {
			config.navigation = {
				nextEl: wrapper ? wrapper.querySelector( '.swiper-button-next' ) : null,
				prevEl: wrapper ? wrapper.querySelector( '.swiper-button-prev' ) : null,
			};
		}

		if ( options.pagination ) {
			config.pagination = {
				el: wrapper ? wrapper.querySelector( '.swiper-pagination' ) : el.querySelector( '.swiper-pagination' ),
				clickable: true,
			};
		}

		syncWrapperBackground( el );
		new Swiper( el, config ); // eslint-disable-line no-undef
	}

	/**
	 * Initialise all sliders inside a given context.
	 *
	 * @param {HTMLElement|Document} context
	 */
	function initAll( context ) {
		if ( ! context ) {
			return;
		}

		context.querySelectorAll( '.ut-hero-slider' ).forEach( initSlider );
	}

	/**
	 * Boot: run after Elementor frontend is ready.
	 */
	function boot() {
		initAll( document );
	}

	/**
	 * Register Elementor preview hooks.
	 */
	function registerElementorHooks() {
		if ( hooksRegistered ) {
			return;
		}

		if ( typeof elementorFrontend === 'undefined' || ! elementorFrontend.hooks ) { // eslint-disable-line no-undef
			return;
		}

		hooksRegistered = true;

		elementorFrontend.hooks.addAction( 'frontend/element_ready/urbantaxi_hero_slider.default', function ( $scope ) { // eslint-disable-line no-undef
			const context = $scope && $scope[ 0 ] ? $scope[ 0 ] : document;
			initAll( context );
		} );

		// Fallback for editor rerenders/live-preview updates.
		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) { // eslint-disable-line no-undef
			const context = $scope && $scope[ 0 ] ? $scope[ 0 ] : document;
			if ( context.querySelector && context.querySelector( '.ut-hero-slider' ) ) {
				initAll( context );
			}
		} );

		initAll( document );
	}

	// Works both in normal page load and inside Elementor editor preview.
	document.addEventListener( 'DOMContentLoaded', boot );

	if ( typeof jQuery !== 'undefined' ) {
		jQuery( window ).on( 'elementor/frontend/init', function () {
			registerElementorHooks();
		} );
	}

	// If Elementor is already ready before this file runs, still register hooks.
	registerElementorHooks();
}() );
