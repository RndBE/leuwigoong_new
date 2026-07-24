<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<title>Peta 3D DI Leuwigoong</title>
		<link rel="icon" href="<?php echo base_url() ?>image/logopu 4.png">
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-flags.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-payments.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-vendors.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/demo.min.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet"/>
		<script src="https://stesy.beacontelemetry.com/assets/code/tabler.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/demo.min.js" defer></script>
		<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
		<style>
			:root {
				--map-bg: #d5ecf0;
				--brand-blue: #303481;
				--brand-yellow: #ffd615;
				--panel: rgba(48, 52, 129, 0.25);
				--panel-strong: rgba(48, 52, 129, 0.50);
				--text: #ffffff;
				--map-image-width: 3898;
				--map-image-height: 2206;
			}

			* {
				box-sizing: border-box;
			}

			body {
				margin: 0;
				color: var(--text);
				background: var(--map-bg);
				overflow: hidden;
			}

			.peta3d-page {
				position: relative;
				width: 100vw;
				height: 100vh;
				background: #c8e7ee;
			}

			.peta3d-page.edit-mode .marker {
				cursor: grab;
			}

			.peta3d-page.edit-mode .marker:active {
				cursor: grabbing;
			}

			.peta3d-page.edit-mode .marker-pin {
				filter: drop-shadow(0 0 5px rgba(255, 214, 21, 0.95));
			}

			.peta3d-toolbar {
				position: absolute;
				z-index: 20;
				top: 20px;
				left: 20px;
				right: 20px;
				height: 75px;
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 0 20px;
				background: #30348180;
				border: 2px solid #FFD61580;
				border-radius: 5px;
				box-shadow: none;
				font-family: Roboto, Arial, sans-serif;
			}

			.peta3d-brand {
				display: flex;
				align-items: center;
				min-width: 0;
				gap: 12px;
				color: #fff;
			}

			.peta3d-brand img {
				width: auto;
				max-width: 42vw;
				height: 40px;
				object-fit: contain;
				object-position: left center;
			}

			.peta3d-brand .brand-mark {
				display: none;
			}

			.peta3d-title {
				position: absolute;
				width: 1px;
				height: 1px;
				overflow: hidden;
				clip: rect(0, 0, 0, 0);
			}

			.peta3d-actions {
				display: flex;
				align-items: center;
				min-width: 0;
			}

			.weather-chip {
				display: flex;
				align-items: center;
				gap: 0;
				min-height: 52px;
				margin-right: 16px;
				padding: 7px 8px;
				color: #fff;
				border: 1px solid #fff;
				border-radius: 5px;
				background: transparent;
			}

			.weather-chip img {
				width: auto;
				height: 35px;
				object-fit: contain;
			}

			.weather-temp {
				display: flex;
				align-items: flex-start;
				margin-left: 8px;
				font-size: 26px;
				font-weight: 700;
				line-height: 1;
			}

			.weather-temp span {
				font-size: 12px;
				font-weight: 700;
				line-height: 1;
				padding-top: 2px !important;
			}

			.weather-temp h1 {
				margin: 0 !important;
				font-size: inherit;
				font-weight: inherit;
				line-height: inherit;
			}

			.weather-desc {
				margin-left: 14px;
				font-size: 12px;
				font-weight: 700;
				line-height: 1.1;
				white-space: nowrap;
			}

			.weather-desc h5 {
				margin: 0 !important;
				font-size: inherit;
				font-weight: inherit;
				line-height: inherit;
			}

			.nav-action,
			.nav-action-location {
				position: relative;
				display: inline-flex;
				align-items: center;
				gap: 8px;
				margin-right: 16px;
				padding: 7px 0;
				color: #fff;
				background: transparent;
				border: 0;
				font-size: 16px;
				font-weight: 700;
				text-decoration: none;
				white-space: nowrap;
			}

			#peta3dPage .nav-action,
			#peta3dPage .nav-action-location {
				font-size: 16px;
				font-weight: 700;
			}

			#peta3dPage .nav-action > span:not(.nav-link-icon),
			#peta3dPage .nav-action > h3,
			#peta3dPage .nav-action-location > span:not(.nav-link-icon),
			#peta3dPage .nav-action-location > h3 {
				margin: 0 !important;
				font: inherit;
				line-height: inherit;
			}

			.nav-action:hover,
			.nav-action-location:hover {
				color: #fff;
				text-decoration: none;
			}

			.nav-action.active::after,
			.nav-action-location.active::after {
				position: absolute;
				left: 50%;
				bottom: 1px;
				width: 40px;
				height: 2px;
				background: var(--brand-yellow);
				content: "";
				transform: translateX(-50%);
			}

			@media (min-width: 992px) {
				.nav-action.active::after,
				.nav-action-location.active::after {
					left: calc(50% + 15px);
					width: calc(100% - 30px);
				}
			}

			.nav-action svg,
			.nav-action-location svg {
				width: 22px;
				height: 22px;
				flex: 0 0 auto;
				margin-right: 0 !important;
			}

			.map-nav-item {
				display: flex;
				flex-direction: column;
				align-items: center;
			}

			.download-nav-3d {
				margin-right: 16px;
			}

			.download-nav-3d .nav-action,
			.download-nav-location .nav-action-location {
				margin-right: 0;
			}

			@media (min-width: 769px) {
				.peta3d-toolbar .weather-chip-location {
					min-height: 52px;
					margin-right: 16px;
					padding: 7px 8px;
				}

				.peta3d-toolbar .weather-chip-location img {
					height: 35px;
				}

				.peta3d-toolbar .weather-temp-location {
					margin-left: 8px;
					font-size: 26px;
				}

				.peta3d-toolbar .weather-temp-location span {
					font-size: 12px;
				}

				.peta3d-toolbar .weather-desc-location {
					margin-left: 14px;
					font-size: 12px;
				}

				#peta3dPage .peta3d-toolbar .nav-action-location {
					gap: 8px;
					margin-right: 16px;
					font-size: 16px;
				}

				#peta3dPage .peta3d-toolbar .nav-action-location > span:not(.nav-link-icon),
				#peta3dPage .peta3d-toolbar .nav-action-location > h3 {
					position: relative;
					top: 1px;
				}

				#peta3dPage .peta3d-toolbar .nav-action-location svg {
					width: 22px;
					height: 22px;
				}

				.peta3d-toolbar .download-nav-location {
					margin-right: 16px;
				}

				#peta3dPage .peta3d-toolbar .download-nav-location .nav-action-location {
					margin-right: 0 !important;
				}
			}

			.map-tools {
				position: absolute;
				z-index: 35;
				right: 20px;
				bottom: 24px;
				display: grid;
				gap: 8px;
				justify-items: end;
			}

			.edit-button {
				color: #fff;
				background: rgba(48, 52, 129, 0.86);
				border: 2px solid rgba(255, 214, 21, 0.65);
				border-radius: 5px;
				padding: 9px 13px;
				font-size: 13px;
				font-weight: 800;
			}

			.edit-button.active {
				color: #1f2937;
				background: var(--brand-yellow);
				border-color: rgba(255, 214, 21, 0.85);
			}

			.zoom-controls {
				display: grid;
				grid-template-columns: 36px 36px 36px;
				gap: 6px;
				padding: 6px;
				background: rgba(48, 52, 129, 0.62);
				border: 2px solid rgba(255, 214, 21, 0.45);
				border-radius: 5px;
			}

			.zoom-button {
				width: 36px;
				height: 36px;
				display: grid;
				place-items: center;
				color: #fff;
				background: rgba(48, 52, 129, 0.86);
				border: 1px solid rgba(255, 214, 21, 0.65);
				border-radius: 5px;
				font-size: 22px;
				font-weight: 800;
				line-height: 1;
			}

			.zoom-button:disabled {
				opacity: 0.55;
				cursor: not-allowed;
			}

			.zoom-button svg {
				width: 18px;
				height: 18px;
			}

			.label-toggle-button {
				width: 42px;
				height: 42px;
				display: grid;
				place-items: center;
				color: #fff;
				background: rgba(48, 52, 129, 0.86);
				border: 2px solid rgba(255, 214, 21, 0.65);
				border-radius: 5px;
				padding: 0;
			}

			.label-toggle-button.active {
				color: #1f2937;
				background: var(--brand-yellow);
				border-color: rgba(255, 214, 21, 0.85);
			}

			.label-toggle-button svg {
				width: 21px;
				height: 21px;
			}

			.map-search {
				position: absolute;
				z-index: 34;
				left: 20px;
				top: 112px;
				width: min(285px, calc(100vw - 40px));
				color: #1f2c38;
			}

			.map-search-field {
				display: flex;
				align-items: center;
				gap: 8px;
				height: 38px;
				padding: 0 10px;
				background: rgba(48, 52, 129, 0.62);
				border: 2px solid rgba(255, 214, 21, 0.55);
				border-radius: 5px;
				box-shadow: 0 5px 16px rgba(18, 31, 54, 0.12);
			}

			.map-search-field svg {
				width: 16px;
				height: 16px;
				color: #fff;
				flex: 0 0 auto;
			}

			.map-search input {
				width: 100%;
				min-width: 0;
				color: #fff;
				background: transparent;
				border: 0;
				outline: 0;
				font-size: 13px;
				font-weight: 600;
			}

			.map-search input::placeholder {
				color: rgba(255, 255, 255, 0.78);
				font-weight: 500;
			}

			.map-search-results {
				display: none;
				margin-top: 6px;
				max-height: 215px;
				overflow: auto;
				background: rgba(48, 52, 129, 0.74);
				border: 1px solid rgba(255, 214, 21, 0.76);
				border-radius: 5px;
				box-shadow: 0 8px 22px rgba(18, 31, 54, 0.20);
				backdrop-filter: blur(5px);
			}

			.map-search-results.show {
				display: block;
			}

			.search-result {
				width: 100%;
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 10px;
				padding: 9px 10px;
				color: #fff;
				background: transparent;
				border: 0;
				border-bottom: 1px solid rgba(255, 214, 21, 0.18);
				text-align: left;
			}

			.search-result:last-child {
				border-bottom: 0;
			}

			.search-result:hover,
			.search-result:focus {
				background: rgba(255, 214, 21, 0.16);
				outline: 0;
			}

			.search-result strong {
				display: block;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				font-size: 12px;
				font-weight: 700;
			}

			.search-result span {
				flex: 0 0 auto;
				color: rgba(255, 255, 255, 0.72);
				font-size: 11px;
				font-weight: 600;
			}

			.search-empty {
				padding: 10px;
				color: rgba(255, 255, 255, 0.82);
				font-size: 12px;
				font-weight: 600;
			}

			.position-toast {
				position: absolute;
				z-index: 40;
				left: 50%;
				bottom: 24px;
				padding: 9px 14px;
				color: #fff;
				background: rgba(48, 52, 129, 0.92);
				border: 2px solid rgba(255, 214, 21, 0.65);
				border-radius: 5px;
				font-size: 13px;
				font-weight: 800;
				opacity: 0;
				pointer-events: none;
				transform: translate(-50%, 8px);
				transition: opacity .18s ease, transform .18s ease;
			}

			.position-toast.show {
				opacity: 1;
				transform: translate(-50%, 0);
			}

			.edit-banner {
				position: absolute;
				z-index: 34;
				left: 50%;
				top: 112px;
				display: flex;
				align-items: center;
				gap: 8px;
				padding: 8px 12px;
				color: #1f2937;
				background: rgba(255, 214, 21, 0.94);
				border: 1px solid rgba(255, 255, 255, 0.78);
				border-radius: 5px;
				font-size: 13px;
				font-weight: 800;
				opacity: 0;
				pointer-events: none;
				transform: translate(-50%, -8px);
				transition: opacity .18s ease, transform .18s ease;
			}

			.edit-banner.show {
				opacity: 1;
				transform: translate(-50%, 0);
			}

			.map-shell {
				position: absolute;
				inset: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				padding: 0;
				background: #c8e7ee;
			}

			.map-canvas {
				position: relative;
				width: 100vw;
				height: 100vh;
				aspect-ratio: var(--map-image-width) / var(--map-image-height);
				box-shadow: none;
				overflow: hidden;
				--map-zoom: 1;
				--callout-scale: 1;
				--marker-scale: 1;
				--marker-hover-scale: 1.08;
				--label-zoom-scale: 1;
				--label-min-width: 125px;
				--label-max-width: 180px;
				--label-compact-min-width: 95px;
				--label-compact-max-width: 140px;
				--label-padding-y: 6px;
				--label-padding-x: 8px;
				--label-compact-padding-y: 5px;
				--label-compact-padding-x: 7px;
				--label-title-size: 12px;
				--label-status-size: 10px;
				--map-pan-x: 0px;
				--map-pan-y: 0px;
				--map-frame-width: 100%;
				--map-frame-height: 100%;
				--map-frame-left: 0px;
				--map-frame-top: 0px;
				touch-action: none;
				user-select: none;
			}

			.map-canvas.is-pannable {
				cursor: grab;
			}

			.map-canvas.is-panning {
				cursor: grabbing;
			}

			.map-canvas > picture,
			.map-canvas > picture > img {
				display: block;
				width: 100%;
				height: 100%;
				pointer-events: none;
			}

			.map-canvas > picture > img {
				object-fit: cover;
				object-position: center center;
				user-select: none;
				-webkit-user-drag: none;
			}

			.map-canvas > picture,
			.marker-layer,
			#markerCallout {
				transform: translate(var(--map-pan-x), var(--map-pan-y)) scale(var(--map-zoom));
				transform-origin: center center;
			}

			.map-canvas.is-animating-view > picture,
			.map-canvas.is-animating-view .marker-layer,
			.map-canvas.is-animating-view #markerCallout {
				transition: transform .48s cubic-bezier(.22, .88, .28, 1);
			}

			.marker-layer {
				position: absolute;
				left: var(--map-frame-left);
				top: var(--map-frame-top);
				width: var(--map-frame-width);
				height: var(--map-frame-height);
				pointer-events: none;
			}

			.marker {
				pointer-events: auto;
			}

			#markerCallout {
				position: absolute;
				left: var(--map-frame-left);
				top: var(--map-frame-top);
				width: var(--map-frame-width);
				height: var(--map-frame-height);
				pointer-events: none;
			}

			#markerCallout .callout {
				pointer-events: auto;
			}

			.marker {
				position: absolute;
				left: calc(var(--x) * 1%);
				top: calc(var(--y) * 1%);
				z-index: 4;
				width: clamp(34px, 2.55vw, 45px);
				height: clamp(34px, 2.55vw, 45px);
				transform: translate(-50%, -100%);
				border: 0;
				background: transparent;
				cursor: pointer;
				text-align: left;
			}

			.marker-pin {
				display: block;
				width: clamp(34px, 2.55vw, 45px);
				height: clamp(34px, 2.55vw, 45px);
				filter: drop-shadow(0 3px 5px rgba(18, 31, 54, 0.22));
				transform: scale(var(--marker-scale));
				transform-origin: center bottom;
				transition: transform .16s ease, filter .16s ease;
			}

			.marker-pin img {
				display: block;
				width: 100%;
				height: 100%;
				object-fit: contain;
				pointer-events: none;
			}

			.marker-label {
				position: absolute;
				left: calc(100% + 5px);
				top: 50%;
				transform: translateY(-50%) scale(var(--label-zoom-scale));
				transform-origin: left center;
				max-width: var(--label-max-width);
				min-width: var(--label-min-width);
				padding: var(--label-padding-y) var(--label-padding-x);
				border-radius: 6px;
				background: rgba(255, 255, 255, 0.78);
				box-shadow: none;
				border: 0;
				font-size: 11px;
				line-height: 1.15;
				backdrop-filter: blur(1px);
				transition: background .16s ease, opacity .16s ease, transform .16s ease;
			}

			.marker:hover .marker-pin,
			.marker:focus-visible .marker-pin {
				transform: translateY(-2px) scale(var(--marker-hover-scale));
				filter: drop-shadow(0 0 5px rgba(255, 214, 21, 0.80)) drop-shadow(0 4px 7px rgba(18, 31, 54, 0.24));
			}

			.marker:hover .marker-label,
			.marker:focus-visible .marker-label {
				background: rgba(255, 255, 255, 0.94);
			}

			.marker-layer.is-resolving .marker-label {
				transition: none;
			}

			.peta3d-page.has-callout .marker.active .marker-label {
				opacity: 0;
				pointer-events: none;
				transform: translateY(-50%) translateX(0) scale(var(--label-zoom-scale));
			}

			.peta3d-page.has-callout .marker.active.label-left .marker-label {
				transform: translateY(-50%) translateX(0) scale(var(--label-zoom-scale));
			}

			.peta3d-page.has-callout .marker.active.label-top .marker-label,
			.peta3d-page.has-callout .marker.active.label-bottom .marker-label {
				transform: translateX(-50%) translateY(0) scale(var(--label-zoom-scale));
			}

			.peta3d-page.labels-compact .marker-label {
				min-width: var(--label-compact-min-width);
				max-width: var(--label-compact-max-width);
				padding: var(--label-compact-padding-y) var(--label-compact-padding-x);
			}

			.peta3d-page.labels-hidden .marker-label {
				opacity: 0 !important;
				pointer-events: none;
			}

			.peta3d-page.labels-compact .marker:not(.active):not(:hover):not(:focus-visible) .marker-label {
				opacity: 0;
				pointer-events: none;
				transform: translateY(-50%) translateX(-4px) scale(var(--label-zoom-scale));
			}

			.peta3d-page.labels-compact .marker.label-left:not(.active):not(:hover):not(:focus-visible) .marker-label {
				transform: translateY(-50%) translateX(4px) scale(var(--label-zoom-scale));
			}

			.peta3d-page.labels-compact .marker.label-top:not(.active):not(:hover):not(:focus-visible) .marker-label,
			.peta3d-page.labels-compact .marker.label-bottom:not(.active):not(:hover):not(:focus-visible) .marker-label {
				transform: translateX(-50%) translateY(4px) scale(var(--label-zoom-scale));
			}

			.peta3d-page.labels-compact .marker.label-top-right:not(.active):not(:hover):not(:focus-visible) .marker-label,
			.peta3d-page.labels-compact .marker.label-bottom-right:not(.active):not(:hover):not(:focus-visible) .marker-label {
				transform: translateX(-4px) scale(var(--label-zoom-scale));
			}

			.peta3d-page.labels-compact .marker.label-top-left:not(.active):not(:hover):not(:focus-visible) .marker-label,
			.peta3d-page.labels-compact .marker.label-bottom-left:not(.active):not(:hover):not(:focus-visible) .marker-label {
				transform: translateX(4px) scale(var(--label-zoom-scale));
			}

			.peta3d-page.labels-compact .marker-label span {
				display: none;
			}

			.peta3d-page.labels-compact .marker.active .marker-label span,
			.peta3d-page.labels-compact .marker:hover .marker-label span,
			.peta3d-page.labels-compact .marker:focus-visible .marker-label span {
				display: flex;
			}

			.marker.label-left .marker-label {
				left: auto;
				right: calc(100% + 5px);
				transform-origin: right center;
			}

			.marker.label-top .marker-label {
				left: 50%;
				top: auto;
				bottom: calc(100% + 5px);
				transform: translateX(-50%) scale(var(--label-zoom-scale));
				transform-origin: center bottom;
			}

			.marker.label-bottom .marker-label {
				left: 50%;
				top: calc(100% + 5px);
				transform: translateX(-50%) scale(var(--label-zoom-scale));
				transform-origin: center top;
			}

			.marker.label-top-right .marker-label {
				left: calc(100% + 5px);
				top: auto;
				bottom: calc(60% + 5px);
				transform: scale(var(--label-zoom-scale));
				transform-origin: left bottom;
			}

			.marker.label-bottom-right .marker-label {
				left: calc(100% + 5px);
				top: calc(60% + 5px);
				transform: scale(var(--label-zoom-scale));
				transform-origin: left top;
			}

			.marker.label-top-left .marker-label {
				left: auto;
				right: calc(100% + 5px);
				top: auto;
				bottom: calc(60% + 5px);
				transform: scale(var(--label-zoom-scale));
				transform-origin: right bottom;
			}

			.marker.label-bottom-left .marker-label {
				left: auto;
				right: calc(100% + 5px);
				top: calc(60% + 5px);
				transform: scale(var(--label-zoom-scale));
				transform-origin: right top;
			}

			.marker-label strong {
				display: block;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				color: #2e322c;
				font-size: var(--label-title-size);
				font-weight: 600;
			}

			.marker-label span {
				display: flex;
				align-items: center;
				gap: 5px;
				margin-top: 2px;
				color: #263226;
				font-size: var(--label-status-size);
				font-weight: 400;
				white-space: nowrap;
			}

			.marker-label span::before {
				width: 6px;
				height: 6px;
				border-radius: 50%;
				background: #0abf53;
				content: "";
			}

			.marker.offline .marker-label span::before {
				background: #d13b3b;
			}

			.marker.maintenance .marker-label span::before {
				background: #ffab0f;
			}

			.marker.active .marker-pin {
				filter: drop-shadow(0 0 5px rgba(255, 214, 21, 0.95)) drop-shadow(0 3px 5px rgba(18, 31, 54, 0.22));
			}

			.status-pill {
				display: inline-flex;
				align-items: center;
				gap: 5px;
				padding: 3px 7px;
				border-radius: 999px;
				font-size: 11px;
				font-weight: 800;
				color: #0d7e3b;
				background: #e7f8ee;
			}

			.status-pill::before {
				width: 7px;
				height: 7px;
				border-radius: 50%;
				background: currentColor;
				content: "";
			}

			.status-pill.offline {
				color: #b82727;
				background: #ffecec;
			}

			.callout {
				position: absolute;
				left: calc(var(--x) * 1%);
				top: calc(var(--y) * 1%);
				z-index: 30;
				width: min(246px, calc(100vw - 32px));
				color: #25313d;
				background: rgba(255, 255, 255, 0.94);
				border: 1px solid rgba(255, 255, 255, 0.96);
				border-radius: 8px;
				box-shadow: 0 8px 18px rgba(18, 31, 54, 0.16);
				backdrop-filter: blur(6px);
				transform: translate(14px, -50%) scale(var(--callout-scale));
				transform-origin: left center;
			}

			.callout::before {
				display: none;
				content: "";
			}

			.callout.is-left {
				transform: translate(calc(-100% - 14px), -50%) scale(var(--callout-scale));
				transform-origin: right center;
			}

			.callout.is-left::before {
				left: auto;
				right: -10px;
				border-left: 0;
				border-bottom: 0;
				border-right: 1px solid #d9dee6;
				border-top: 1px solid #d9dee6;
			}

			.callout.is-top {
				transform: translate(-50%, calc(-100% - 14px)) scale(var(--callout-scale));
				transform-origin: center bottom;
			}

			.callout.is-top::before {
				left: 50%;
				top: auto;
				bottom: -10px;
				border-left: 0;
				border-bottom: 0;
				border-right: 1px solid #d9dee6;
				border-top: 1px solid #d9dee6;
				transform: translateX(-50%) rotate(135deg);
			}

			.callout.is-bottom {
				transform: translate(-50%, 14px) scale(var(--callout-scale));
				transform-origin: center top;
			}

			.callout.is-bottom::before {
				left: 50%;
				top: -10px;
				transform: translateX(-50%) rotate(135deg);
			}

			.callout-head {
				position: relative;
				z-index: 1;
				display: flex;
				align-items: flex-start;
				justify-content: space-between;
				gap: 10px;
				padding: 9px 10px 6px;
			}

			.callout-title {
				margin: 0;
				color: #1f2c38;
				font-size: 12px;
				font-weight: 700;
				line-height: 1.2;
			}

			.callout-close {
				width: 22px;
				height: 22px;
				display: grid;
				place-items: center;
				flex: 0 0 auto;
				color: #6b7280;
				background: transparent;
				border: 0;
				border-radius: 4px;
				font-size: 24px;
				font-weight: 300;
				line-height: 1;
			}

			.callout-body {
				position: relative;
				z-index: 1;
				padding: 0 10px 9px;
			}

			.callout-summary {
				display: block;
				margin: 7px 0 8px;
				padding: 8px 9px;
				background: rgba(48, 52, 129, 0.07);
				border-radius: 6px;
			}

			.callout-summary span {
				display: block;
				color: #667085;
				font-size: 10px;
				font-weight: 700;
				text-transform: uppercase;
			}

			.callout-summary strong {
				display: block;
				margin-top: 2px;
				color: #1f2c38;
				font-size: 18px;
				font-weight: 700;
				line-height: 1.2;
			}

			.callout-gates {
				display: grid;
				grid-template-columns: repeat(2, minmax(0, 1fr));
				gap: 6px;
				margin: 7px 0 8px;
			}

			.callout-gate {
				padding: 7px 6px;
				border-radius: 6px;
				background: rgba(48, 52, 129, 0.07);
				text-align: center;
			}

			.callout-gate strong {
				display: block;
				color: #1f2c38;
				font-size: 15px;
				font-weight: 800;
				line-height: 1.1;
			}

			.callout-gate span {
				display: block;
				margin-top: 3px;
				color: #667085;
				font-size: 9.5px;
				font-weight: 600;
				line-height: 1.15;
			}

			.callout-meta {
				display: flex;
				align-items: center;
				flex-wrap: wrap;
				gap: 6px;
			}

			.callout-status {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				padding: 3px 7px;
				border-radius: 999px;
				color: #0d7e3b;
				background: #e7f8ee;
				font-size: 10px;
				font-weight: 700;
				white-space: nowrap;
			}

			.callout-status::before {
				width: 7px;
				height: 7px;
				border-radius: 50%;
				background: currentColor;
				content: "";
			}

			.callout-status.offline {
				color: #b82727;
				background: #ffecec;
			}

			.callout-status.maintenance {
				color: #9a5a00;
				background: #fff1d6;
			}

			.callout-sd {
				display: inline-flex;
				align-items: center;
				padding: 3px 7px;
				border-radius: 999px;
				color: #4b5563;
				background: rgba(48, 52, 129, 0.08);
				font-size: 10px;
				font-weight: 700;
				white-space: nowrap;
			}

			.callout-time {
				margin-top: 6px;
				color: #667085;
				font-size: 10px;
				font-weight: 600;
			}

			.callout-actions {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 8px;
				margin-top: 8px;
			}

			.callout-link {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				gap: 6px;
				color: #005eb8;
				background: rgba(0, 94, 184, 0.08);
				border: 1px solid rgba(0, 94, 184, 0.12);
				border-radius: 5px;
				padding: 5px 7px;
				font-size: 11px;
				font-weight: 700;
				text-decoration: none;
				white-space: nowrap;
				flex: 1 1 0;
			}

			.callout-link:hover {
				color: #004f9b;
				background: rgba(0, 94, 184, 0.13);
				text-decoration: none;
			}

			.callout-link svg {
				width: 16px;
				height: 16px;
				flex: 0 0 auto;
			}

			.empty-state {
				padding: 18px 10px;
				color: rgba(255, 255, 255, 0.75);
				text-align: center;
			}

			@media (max-width: 992px) {
				body {
					overflow: auto;
				}

				.peta3d-page {
					min-height: 100vh;
					height: auto;
					padding-bottom: 20px;
				}

				.peta3d-toolbar,
				.map-shell {
					position: relative;
					inset: auto;
				}

				.peta3d-toolbar {
					margin: 10px;
					flex-wrap: wrap;
				}

				.map-search {
					top: 110px;
					left: 10px;
					width: min(285px, calc(100vw - 20px));
				}

				.map-tools {
					right: 10px;
					bottom: 20px;
				}

				.edit-banner {
					top: 158px;
					font-size: 12px;
				}

				.map-shell {
					padding: 8px 10px;
					overflow-x: auto;
					justify-content: flex-start;
				}

				.map-canvas {
					width: 920px;
					height: calc(920px * var(--map-image-height) / var(--map-image-width));
					max-width: none;
				}

			}

			@media (max-width: 768px) {
				body {
					overflow: hidden;
				}

				.peta3d-page {
					width: 100vw;
					height: 100dvh;
					min-height: 100dvh;
					padding-bottom: 0;
					overflow: hidden;
				}

				.peta3d-toolbar {
					position: absolute;
					top: 10px;
					left: 10px;
					right: 10px;
					height: 64px;
					margin: 0;
					padding: 0 10px;
					flex-wrap: nowrap;
					gap: 7px;
				}

				.peta3d-brand {
					flex: 0 0 auto;
					min-width: 0;
				}

				.peta3d-brand .brand-full {
					display: none;
				}

				.peta3d-brand .brand-mark {
					display: block;
					width: 28px;
					height: 28px;
					max-width: none;
				}

				.peta3d-actions {
					flex: 0 0 auto;
				}

				.map-nav-item {
					display: contents;
				}

				.weather-chip {
					display: flex;
					min-height: 28px;
					margin-right: 6px;
					padding: 2px 5px;
				}

				.weather-chip img {
					height: 20px;
				}

				.weather-temp {
					margin-left: 4px;
					font-size: 16px;
				}

				.weather-temp span {
					font-size: 9px;
					padding-top: 1px;
				}

				.weather-desc {
					margin-left: 6px;
					font-size: 8px;
					line-height: 1.08;
				}

				.nav-action,
				.nav-action-location {
					width: 34px;
					height: 34px;
					justify-content: center;
					margin-right: 5px !important;
					padding: 0 !important;
					font-size: 0 !important;
					gap: 0;
				}

				.nav-action .nav-link-icon,
				.nav-action-location .nav-link-icon {
					display: flex !important;
					align-items: center;
					justify-content: center;
					width: 21px;
					height: 21px;
					margin: 0 !important;
					padding: 0 !important;
				}

				.nav-action svg,
				.nav-action-location svg {
					width: 21px;
					height: 21px;
					margin: 0 !important;
				}

				.nav-action.active::after,
				.nav-action-location.active::after {
					bottom: -5px;
					width: 24px;
				}

				.download-nav-3d,
				.download-nav-location {
					margin-right: 5px;
				}

				.download-nav-3d .dropdown-toggle::after,
				.download-nav-location .dropdown-toggle::after {
					display: none;
				}

				.map-shell {
					position: absolute;
					inset: 0;
					padding: 0;
					overflow-x: auto;
					overflow-y: hidden;
					justify-content: flex-start;
					-webkit-overflow-scrolling: touch;
				}

				.map-canvas {
					width: max(100vw, calc(100dvh * var(--map-image-width) / var(--map-image-height)));
					height: 100dvh;
					flex: 0 0 auto;
					max-width: none;
					touch-action: none;
				}

				.map-search {
					top: 82px;
					left: 10px;
					width: min(230px, calc(100vw - 20px));
				}

				.map-search-field {
					height: 36px;
				}

				.map-search-results {
					max-height: min(220px, calc(100dvh - 170px));
				}

				.callout {
					width: min(230px, calc(100vw - 20px));
				}

				.callout-summary strong {
					font-size: 16px;
				}

				.callout-actions {
					gap: 6px;
				}

				.callout-link {
					padding: 5px 6px;
				}

				.map-tools {
					right: 10px;
					bottom: 12px;
				}

				.zoom-controls {
					grid-template-columns: 34px 34px 34px;
					gap: 5px;
					padding: 5px;
				}

				.zoom-button {
					width: 34px;
					height: 34px;
				}

				.edit-button {
					padding: 8px 11px;
					font-size: 12px;
				}

				.label-toggle-button {
					width: 38px;
					height: 38px;
				}

				.edit-banner {
					top: 122px;
					max-width: calc(100vw - 20px);
				}
			}

		</style>
	</head>
	<body>
		<div class="peta3d-page" id="peta3dPage">
			<header class="peta3d-toolbar">
				<div class="peta3d-brand peta-map-brand">
					<img class="brand-full" src="<?php echo base_url() ?>image/peta_lokasi.svg" alt="DI Leuwigoong">
					<img class="brand-mark" src="<?php echo base_url() ?>image/logopu 4.png" alt="DI Leuwigoong">
					<h1 class="peta3d-title">Peta 3D DI Leuwigoong</h1>
				</div>
				<div class="peta3d-actions peta-map-actions">
					<div class="weather-chip weather-chip-location">
						<img src="<?php echo isset($cuaca->image) ? $cuaca->image : base_url('image/water.png') ?>" alt="Cuaca">
						<div class="weather-temp weather-temp-location"><h1><?php echo isset($cuaca->t) ? $cuaca->t : '-' ?></h1><span>&deg;C</span></div>
						<div class="weather-desc weather-desc-location">
							<h5><?php echo isset($cuaca->weather_desc) ? $cuaca->weather_desc : 'Cuaca' ?></h5>
							<h5>Angin : <?php echo isset($cuaca->ws) ? $cuaca->ws : '-' ?> km/h</h5>
						</div>
					</div>
					<div class="map-nav-item d-flex flex-column align-items-center">
						<a class="nav-action nav-action-location" href="<?php echo base_url('beranda') ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16" class="me-2"><path fill="currentColor" d="M6.906.664a1.749 1.749 0 0 1 2.187 0l5.25 4.2c.415.332.657.835.657 1.367v7.019A1.75 1.75 0 0 1 13.25 15h-3.5a.75.75 0 0 1-.75-.75V9H7v5.25a.75.75 0 0 1-.75.75h-3.5A1.75 1.75 0 0 1 1 13.25V6.23c0-.531.242-1.034.657-1.366l5.25-4.2Zm1.25 1.171a.25.25 0 0 0-.312 0l-5.25 4.2a.25.25 0 0 0-.094.196v7.019c0 .138.112.25.25.25H5.5V8.25a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v5.25h2.75a.25.25 0 0 0 .25-.25V6.23a.25.25 0 0 0-.094-.195Z"/></svg>
							<span class="d-none d-lg-inline-block">Dashboard</span>
						</a>
					</div>
					<div class="map-nav-item d-flex flex-column align-items-center">
						<a class="nav-action nav-action-location" href="<?php echo base_url('analisa') ?>">
							<span class="nav-link-icon d-lg-inline-block text-white">
								<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>
							</span>
							<span class="d-none d-lg-inline-block">Peta Lokasi</span>
						</a>
					</div>
					<div class="map-nav-item d-flex flex-column align-items-center">
						<button class="nav-action nav-action-location active" type="button">
							<span class="nav-link-icon d-lg-inline-block text-white">
								<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2l9 5l-9 5l-9 -5l9 -5z"/><path d="m3 12l9 5l9 -5"/><path d="m3 17l9 5l9 -5"/></svg>
							</span>
							<span class="d-none d-lg-inline-block">Peta 3D</span>
						</button>
					</div>
					<div class="dropdown px-0 download-nav-3d download-nav-location">
						<button type="button" class="nav-action nav-action-location dropdown-toggle" data-bs-toggle="dropdown">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" class="me-2" height="22" viewBox="0 0 26 26"><g fill="none"><path d="M24 0v24H0V0h24Z"/><path fill="currentColor" d="M20 14.5a1.5 1.5 0 0 1 1.5 1.5v4a2.5 2.5 0 0 1-2.5 2.5H5A2.5 2.5 0 0 1 2.5 20v-4a1.5 1.5 0 0 1 3 0v3.5h13V16a1.5 1.5 0 0 1 1.5-1.5Zm-8-13A1.5 1.5 0 0 1 13.5 3v9.036l1.682-1.682a1.5 1.5 0 0 1 2.121 2.12l-4.066 4.067a1.75 1.75 0 0 1-2.474 0l-4.066-4.066a1.5 1.5 0 0 1 2.121-2.121l1.682 1.682V3A1.5 1.5 0 0 1 12 1.5Z"/></g></svg>
							<h3 class="mb-0 fw-bold d-none d-lg-inline-block">Unduh</h3>
						</button>
						<div class="dropdown-menu fw-bold border-white">
							<a class="dropdown-item" href="<?php echo base_url() ?>" target="_blank">
								Android App
							</a>
						</div>
					</div>
					<a class="nav-action nav-action-location" href="<?php echo base_url('login/logout') ?>">
						<svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="22" height="22" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0h24Z"/><path fill="currentColor" d="M12 2.5a1.5 1.5 0 0 1 0 3H7a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h4.5a1.5 1.5 0 0 1 0 3H7A3.5 3.5 0 0 1 3.5 18V6A3.5 3.5 0 0 1 7 2.5Zm6.06 5.61l2.829 2.83a1.5 1.5 0 0 1 0 2.12l-2.828 2.83a1.5 1.5 0 1 1-2.122-2.122l.268-.268H12a1.5 1.5 0 0 1 0-3h4.207l-.268-.268a1.5 1.5 0 1 1 2.122-2.121Z"/></g></svg>
						<span class="d-none d-lg-inline-block">Keluar</span>
					</a>
				</div>
			</header>

			<main class="map-shell">
				<div class="map-canvas" id="mapCanvas">
					<picture>
						<source srcset="<?php echo base_url() ?>image/3D_Bendungan_DI_Leuwigoong_3200.webp?v=3200x1811" type="image/webp" width="3200" height="1811">
						<img src="<?php echo base_url() ?>image/3D_Bendungan_DI_Leuwigoong.svg?v=3898x2206" alt="Peta 3D DI Leuwigoong" width="3898" height="2206">
					</picture>
					<div class="marker-layer" id="markerLayer"></div>
					<div id="markerCallout"></div>
				</div>
			</main>
			<div class="map-search" id="mapSearch">
				<label class="map-search-field" for="markerSearchInput">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21 21l-4.34 -4.34"/><circle cx="11" cy="11" r="8"/></svg>
					<input type="search" id="markerSearchInput" autocomplete="off" placeholder="Cari pos">
				</label>
				<div class="map-search-results" id="markerSearchResults"></div>
			</div>
			<div class="edit-banner" id="editBanner">Mode atur titik aktif</div>
			<div class="map-tools">
				<div class="zoom-controls" aria-label="Zoom peta">
					<button class="zoom-button" type="button" id="zoomOut" aria-label="Zoom out">-</button>
					<button class="zoom-button" type="button" id="zoomIn" aria-label="Zoom in">+</button>
					<button class="zoom-button" type="button" id="resetView" aria-label="Lihat semua" title="Lihat semua">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/></svg>
					</button>
				</div>
				<button class="label-toggle-button" type="button" id="toggleMarkerLabels" aria-label="Sembunyikan label titik" title="Sembunyikan label titik" aria-pressed="false">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"/><path d="M16.68 16.68A10.82 10.82 0 0 1 12 18c-5 0 -9 -6 -9 -6a18.45 18.45 0 0 1 5.32 -4.68"/><path d="M10.88 5.08A10.94 10.94 0 0 1 12 5c5 0 9 6 9 6a18.5 18.5 0 0 1 -2.5 3.17"/><path d="M3 3l18 18"/></svg>
				</button>
				<button class="edit-button" type="button" id="toggleEditMarkers">Atur Titik</button>
			</div>
			<div class="position-toast" id="positionToast">Posisi marker tersimpan</div>
		</div>

		<script>
			const initialMarkers = <?php echo json_encode($markers); ?>;
			const markerUrl = "<?php echo base_url('peta3d/markers'); ?>";
			const saveMarkerUrl = "<?php echo base_url('peta3d/save_marker_position'); ?>";
			const markerIconBaseUrl = "<?php echo base_url('pin_marker/3D/'); ?>";
			let markers = Array.isArray(initialMarkers) ? initialMarkers : [];
			let selectedId = null;
			let editMode = false;
			let labelsVisible = true;
			let draggingId = null;
			let toastTimer = null;
			let zoomLevel = 1;
			const minZoom = 1;
			const maxZoom = 1.8;
			const zoomStep = 0.15;
			let panX = 0;
			let panY = 0;
			let isPanning = false;
			let isShellPanning = false;
			let panStart = { x: 0, y: 0, panX: 0, panY: 0 };
			let shellPanStart = { x: 0, scrollLeft: 0 };
			let activePointers = {};
			let pinchStart = null;
			let focusAnimationTimer = null;
			let zoomAnimationTimer = null;
			let mobileInitialViewApplied = false;
			const mapImageSize = { width: 3898, height: 2206 };
			const mobileInitialView = { x: 0.65 };

			function escapeHtml(value) {
				return String(value ?? '').replace(/[&<>"']/g, function(match) {
					return {
						'&': '&amp;',
						'<': '&lt;',
						'>': '&gt;',
						'"': '&quot;',
						"'": '&#039;'
					}[match];
				});
			}

			function markerStatusClass(marker) {
				const rawStatus = String(marker.status_class || marker.status || '').toLowerCase();
				if (rawStatus.indexOf('maintenance') !== -1 || rawStatus.indexOf('perbaikan') !== -1) {
					return 'maintenance';
				}
				return rawStatus.indexOf('online') !== -1 ? 'online' : 'offline';
			}

			function connectionText(marker) {
				const statusClass = markerStatusClass(marker);
				if (statusClass === 'maintenance') {
					return 'Perbaikan';
				}
				return statusClass === 'online' ? 'Koneksi Terhubung' : 'Koneksi Terputus';
			}

			function shortStatusText(marker) {
				const statusClass = markerStatusClass(marker);
				if (statusClass === 'maintenance') {
					return 'Perbaikan';
				}
				return statusClass === 'online' ? 'Online' : 'Offline';
			}

			function markerIcon(marker) {
				const statusClass = markerStatusClass(marker);
				const statusSuffix = statusClass === 'maintenance' ? 'maintenance' : (statusClass === 'online' ? 'on' : 'off');
				const markerName = String(marker.nama_lokasi || '').toLowerCase();
				const markerController = String(marker.controller || '').toLowerCase();
				const iconPrefix = markerController === 'awgc' || markerName.indexOf('awgc') !== -1 ? 'awgc3d' : 'cam3d';
				return markerIconBaseUrl + iconPrefix + '_' + statusSuffix + '.svg';
			}

			function updateZoomState() {
				const mapCanvas = document.getElementById('mapCanvas');
				const progress = Math.max(0, Math.min(1, (zoomLevel - minZoom) / (maxZoom - minZoom)));
				const labelScale = 1 / Math.sqrt(zoomLevel);
				mapCanvas.style.setProperty('--label-zoom-scale', Number(labelScale.toFixed(3)));
				mapCanvas.style.setProperty('--label-min-width', (120 + progress * 26).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-max-width', (145 + progress * 35).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-compact-min-width', (94 + progress * 20).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-compact-max-width', (112 + progress * 26).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-padding-y', (5 + progress * 1.5).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-padding-x', (7 + progress * 2).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-compact-padding-y', (4.4 + progress).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-compact-padding-x', (6.2 + progress * 1.2).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-title-size', (11 + progress * 2).toFixed(1) + 'px');
				mapCanvas.style.setProperty('--label-status-size', (9 + progress * 1.5).toFixed(1) + 'px');
				$('#peta3dPage')
					.toggleClass('labels-compact', zoomLevel < 1.25)
					.toggleClass('labels-hidden', !labelsVisible);
			}

			function isMobileViewport() {
				return window.matchMedia('(max-width: 768px)').matches;
			}

			function pointerDistance(a, b) {
				const dx = a.clientX - b.clientX;
				const dy = a.clientY - b.clientY;
				return Math.sqrt(dx * dx + dy * dy);
			}

			function pointerCenter(a, b) {
				return {
					clientX: (a.clientX + b.clientX) / 2,
					clientY: (a.clientY + b.clientY) / 2
				};
			}

			function activePointerList() {
				return Object.keys(activePointers).map(function(key) {
					return activePointers[key];
				});
			}

			function applyMobileInitialView(force) {
				if (!isMobileViewport()) {
					mobileInitialViewApplied = false;
					return;
				}
				if (mobileInitialViewApplied && !force) {
					return;
				}

				const shell = document.querySelector('.map-shell');
				const canvas = document.getElementById('mapCanvas');
				if (!shell || !canvas || shell.scrollWidth <= shell.clientWidth) {
					return;
				}

				const targetLeft = (canvas.offsetWidth * mobileInitialView.x) - (shell.clientWidth / 2);
				const maxLeft = shell.scrollWidth - shell.clientWidth;
				shell.scrollLeft = Math.max(0, Math.min(maxLeft, targetLeft));
				mobileInitialViewApplied = true;
			}

			function updateMapFrame() {
				const canvas = document.getElementById('mapCanvas');
				const rect = canvas.getBoundingClientRect();
				const imageRatio = mapImageSize.width / mapImageSize.height;
				const canvasRatio = rect.width / rect.height;
				let frameWidth = rect.width;
				let frameHeight = rect.height;

				if (canvasRatio > imageRatio) {
					frameHeight = frameWidth / imageRatio;
				} else {
					frameWidth = frameHeight * imageRatio;
				}

				canvas.style.setProperty('--map-frame-width', frameWidth + 'px');
				canvas.style.setProperty('--map-frame-height', frameHeight + 'px');
				canvas.style.setProperty('--map-frame-left', ((rect.width - frameWidth) / 2) + 'px');
				canvas.style.setProperty('--map-frame-top', ((rect.height - frameHeight) / 2) + 'px');
				return {
					canvasRect: rect,
					width: frameWidth,
					height: frameHeight,
					left: (rect.width - frameWidth) / 2,
					top: (rect.height - frameHeight) / 2
				};
			}

			function applyZoom(options) {
				options = options || {};
				zoomLevel = Math.max(minZoom, Math.min(maxZoom, Number(zoomLevel.toFixed(2))));
				const mapCanvas = document.getElementById('mapCanvas');
				updateMapFrame();
				mapCanvas.style.setProperty('--map-zoom', zoomLevel);
				mapCanvas.style.setProperty('--callout-scale', Number((1 / zoomLevel).toFixed(3)));
				mapCanvas.style.setProperty('--marker-scale', Number((1 / zoomLevel).toFixed(3)));
				mapCanvas.style.setProperty('--marker-hover-scale', Number((1.08 / zoomLevel).toFixed(3)));
				applyPan();
				$('#mapCanvas').toggleClass('is-pannable', zoomLevel > minZoom && !editMode);
				$('#zoomOut').prop('disabled', zoomLevel <= minZoom);
				$('#zoomIn').prop('disabled', zoomLevel >= maxZoom);
				updateZoomState();
				if (options.skipRender) {
					updateActiveMarkers();
					return;
				}
				renderMarkers({ skipCallout: options.skipCallout });
			}

			function setZoom(nextZoom, anchor, options) {
				options = options || {};
				const previousZoom = zoomLevel;
				const next = Math.max(minZoom, Math.min(maxZoom, Number(nextZoom.toFixed(2))));
				const canvas = document.getElementById('mapCanvas');
				const rect = canvas.getBoundingClientRect();

				if (anchor && next > minZoom && previousZoom > 0) {
					const centerX = rect.left + rect.width / 2;
					const centerY = rect.top + rect.height / 2;
					const localX = (anchor.clientX - centerX - panX) / previousZoom + rect.width / 2;
					const localY = (anchor.clientY - centerY - panY) / previousZoom + rect.height / 2;
					panX = anchor.clientX - centerX - (localX - rect.width / 2) * next;
					panY = anchor.clientY - centerY - (localY - rect.height / 2) * next;
				}

				zoomLevel = next;
				if (zoomLevel <= minZoom) {
					panX = 0;
					panY = 0;
				}
				applyZoom({ skipRender: options.skipRender, skipCallout: options.skipCallout });
			}

			function finishZoomInteraction() {
				resolveRenderedLabelCollisions();
				window.requestAnimationFrame(resolveRenderedCalloutPosition);
			}

			function smoothSetZoom(nextZoom) {
				clearTimeout(zoomAnimationTimer);
				$('#mapCanvas').addClass('is-animating-view');
				setZoom(nextZoom, null, { skipRender: true });
				zoomAnimationTimer = setTimeout(function() {
					$('#mapCanvas').removeClass('is-animating-view');
					finishZoomInteraction();
				}, 520);
			}

			function quickSetZoom(nextZoom, anchor) {
				clearTimeout(zoomAnimationTimer);
				$('#mapCanvas').removeClass('is-animating-view');
				setZoom(nextZoom, anchor, { skipRender: true });
				zoomAnimationTimer = setTimeout(finishZoomInteraction, 120);
			}

			function resetView() {
				clearTimeout(focusAnimationTimer);
				clearTimeout(zoomAnimationTimer);
				$('#mapCanvas').addClass('is-animating-view');
				selectedId = null;
				$('#peta3dPage').removeClass('has-callout');
				zoomLevel = minZoom;
				panX = 0;
				panY = 0;
				$('#markerSearchInput').val('');
				$('#markerSearchResults').removeClass('show').empty();
				applyZoom();
				applyMobileInitialView(true);
				focusAnimationTimer = setTimeout(function() {
					$('#mapCanvas').removeClass('is-animating-view');
				}, 520);
			}

			function focusMarker(marker) {
				clearTimeout(focusAnimationTimer);
				clearTimeout(zoomAnimationTimer);
				const canvas = document.getElementById('mapCanvas');
				const frame = updateMapFrame();
				const rect = frame.canvasRect;
				$('#mapCanvas').addClass('is-animating-view');
				zoomLevel = Math.max(1.35, zoomLevel);
				const targetX = frame.left + Number(marker.x) / 100 * frame.width;
				const targetY = frame.top + Number(marker.y) / 100 * frame.height;
				panX = -(targetX - rect.width / 2) * zoomLevel;
				panY = -(targetY - rect.height / 2) * zoomLevel;
				selectedId = String(marker.id_logger);
				applyZoom({ skipRender: true, skipCallout: true });
				updateActiveMarkers();
				$('#markerCallout').empty();
				$('#peta3dPage').removeClass('has-callout');
				focusAnimationTimer = setTimeout(function() {
					$('#mapCanvas').removeClass('is-animating-view');
					updateActiveMarkers();
					renderCallout();
					window.requestAnimationFrame(resolveRenderedCalloutPosition);
				}, 500);
			}

			function clampPan() {
				const canvas = document.getElementById('mapCanvas');
				const rect = canvas.getBoundingClientRect();
				const maxPanX = Math.max(0, (rect.width * (zoomLevel - 1)) / 2);
				const maxPanY = Math.max(0, (rect.height * (zoomLevel - 1)) / 2);
				panX = Math.max(-maxPanX, Math.min(maxPanX, panX));
				panY = Math.max(-maxPanY, Math.min(maxPanY, panY));
			}

			function applyPan() {
				clampPan();
				document.getElementById('mapCanvas').style.setProperty('--map-pan-x', panX + 'px');
				document.getElementById('mapCanvas').style.setProperty('--map-pan-y', panY + 'px');
			}

			function setEditMode(enabled) {
				editMode = enabled;
				selectedId = null;
				draggingId = null;
				$('#peta3dPage').toggleClass('edit-mode', editMode);
				$('#peta3dPage').removeClass('has-callout');
				$('#editBanner').toggleClass('show', editMode);
				$('#mapCanvas').toggleClass('is-pannable', zoomLevel > minZoom && !editMode);
				$('#toggleEditMarkers')
					.toggleClass('active', editMode)
					.text(editMode ? 'Selesai Atur' : 'Atur Titik');
				renderMarkers();
				showToast(editMode ? 'Mode atur titik aktif. Drag marker untuk menyimpan posisi.' : 'Mode atur titik selesai.');
			}

			function markerLabelToggleIcon(visible) {
				if (visible) {
					return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"/><path d="M16.68 16.68A10.82 10.82 0 0 1 12 18c-5 0 -9 -6 -9 -6a18.45 18.45 0 0 1 5.32 -4.68"/><path d="M10.88 5.08A10.94 10.94 0 0 1 12 5c5 0 9 6 9 6a18.5 18.5 0 0 1 -2.5 3.17"/><path d="M3 3l18 18"/></svg>';
				}
				return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0 -.7C3.5 7.35 7.5 5 12 5s8.5 2.35 9.94 6.65a1 1 0 0 1 0 .7C20.5 16.65 16.5 19 12 19s-8.5 -2.35 -9.94 -6.65Z"/><path d="M12 15a3 3 0 1 0 0 -6a3 3 0 0 0 0 6Z"/></svg>';
			}

			function setLabelsVisible(visible) {
				labelsVisible = visible;
				const title = labelsVisible ? 'Sembunyikan label titik' : 'Tampilkan label titik';
				$('#peta3dPage').toggleClass('labels-hidden', !labelsVisible);
				$('#toggleMarkerLabels')
					.toggleClass('active', !labelsVisible)
					.attr('aria-pressed', String(!labelsVisible))
					.attr('aria-label', title)
					.attr('title', title)
					.html(markerLabelToggleIcon(labelsVisible));
				renderMarkers();
				showToast(labelsVisible ? 'Label titik ditampilkan' : 'Label titik disembunyikan');
			}

			function showToast(message) {
				clearTimeout(toastTimer);
				$('#positionToast').text(message).addClass('show');
				toastTimer = setTimeout(function() {
					$('#positionToast').removeClass('show');
				}, 2200);
			}

			function getCanvasPosition(event) {
				const frame = document.getElementById('markerLayer').getBoundingClientRect();
				const x = Math.max(0, Math.min(100, ((event.clientX - frame.left) / frame.width) * 100));
				const y = Math.max(0, Math.min(100, ((event.clientY - frame.top) / frame.height) * 100));
				return {
					x: Number(x.toFixed(2)),
					y: Number(y.toFixed(2))
				};
			}

			function updateMarkerPosition(id, position) {
				const marker = markers.find(function(item) {
					return String(item.id_logger) === String(id);
				});
				if (!marker) {
					return;
				}
				marker.x = position.x;
				marker.y = position.y;
				renderMarkers();
			}

			function saveMarkerPosition(id, position) {
				$.ajax({
					url: saveMarkerUrl,
					method: 'POST',
					dataType: 'json',
					data: {
						id_logger: id,
						x: position.x,
						y: position.y
					}
				}).done(function() {
					showToast('Posisi marker tersimpan');
				}).fail(function() {
					showToast('Gagal menyimpan posisi marker');
				});
			}

			function boxesOverlap(a, b, padding) {
				return !(a.right + padding < b.left || a.left - padding > b.right || a.bottom + padding < b.top || a.top - padding > b.bottom);
			}

			function labelCandidates(marker) {
				const frame = document.getElementById('markerLayer').getBoundingClientRect();
				const canvasStyle = window.getComputedStyle(document.getElementById('mapCanvas'));
				const compact = zoomLevel < 1.25;
				const labelScale = Number.parseFloat(canvasStyle.getPropertyValue('--label-zoom-scale')) || 1;
				const labelMaxWidth = Number.parseFloat(canvasStyle.getPropertyValue(compact ? '--label-compact-max-width' : '--label-max-width')) || 145;
				const labelPaddingY = Number.parseFloat(canvasStyle.getPropertyValue(compact ? '--label-compact-padding-y' : '--label-padding-y')) || 5;
				const titleSize = Number.parseFloat(canvasStyle.getPropertyValue('--label-title-size')) || 11;
				const statusSize = compact ? 0 : (Number.parseFloat(canvasStyle.getPropertyValue('--label-status-size')) || 9);
				const visualZoom = zoomLevel * labelScale;
				const labelWidth = labelMaxWidth * visualZoom;
				const labelHeight = ((labelPaddingY * 2) + titleSize + (statusSize ? statusSize + 4 : 0)) * visualZoom;
				const markerGapX = 50 * visualZoom;
				const markerGapY = 48 * visualZoom;
				const widthPct = labelWidth / frame.width * 100;
				const heightPct = labelHeight / frame.height * 100;
				const gapXPct = markerGapX / frame.width * 100;
				const gapYPct = markerGapY / frame.height * 100;
				const x = Number(marker.x);
				const y = Number(marker.y);

				return [
					{ placement: 'label-right', left: x + gapXPct, top: y - heightPct / 2, right: x + gapXPct + widthPct, bottom: y + heightPct / 2 },
					{ placement: 'label-left', left: x - gapXPct - widthPct, top: y - heightPct / 2, right: x - gapXPct, bottom: y + heightPct / 2 },
					{ placement: 'label-top', left: x - widthPct / 2, top: y - gapYPct - heightPct, right: x + widthPct / 2, bottom: y - gapYPct },
					{ placement: 'label-bottom', left: x - widthPct / 2, top: y + gapYPct, right: x + widthPct / 2, bottom: y + gapYPct + heightPct },
					{ placement: 'label-top-right', left: x + gapXPct, top: y - gapYPct - heightPct, right: x + gapXPct + widthPct, bottom: y - gapYPct },
					{ placement: 'label-bottom-right', left: x + gapXPct, top: y + gapYPct * .5, right: x + gapXPct + widthPct, bottom: y + gapYPct * .5 + heightPct },
					{ placement: 'label-top-left', left: x - gapXPct - widthPct, top: y - gapYPct - heightPct, right: x - gapXPct, bottom: y - gapYPct },
					{ placement: 'label-bottom-left', left: x - gapXPct - widthPct, top: y + gapYPct * .5, right: x - gapXPct, bottom: y + gapYPct * .5 + heightPct },
				];
			}

			function chooseMarkerLayouts(visibleMarkers) {
				const placed = [];
				const layouts = {};
				const sortedMarkers = visibleMarkers.slice().sort(function(a, b) {
					return Number(a.y) - Number(b.y);
				});

				sortedMarkers.forEach(function(marker) {
					const candidates = labelCandidates(marker);
					let chosen = candidates[0];
					let bestScore = Infinity;

					candidates.forEach(function(candidate) {
						let score = 0;
						if (candidate.left < 1) score += (1 - candidate.left) * 10;
						if (candidate.right > 99) score += (candidate.right - 99) * 10;
						if (candidate.top < 9) score += (9 - candidate.top) * 8;
						if (candidate.bottom > 98) score += (candidate.bottom - 98) * 8;

						placed.forEach(function(box) {
							if (boxesOverlap(candidate, box, 0.7)) {
								score += 100;
							}
						});

						if (score < bestScore) {
							bestScore = score;
							chosen = candidate;
						}
					});

					layouts[String(marker.id_logger)] = chosen.placement;
					placed.push(chosen);
				});

				return layouts;
			}

			const markerPlacementClasses = [
				'label-right',
				'label-left',
				'label-top',
				'label-bottom',
				'label-top-right',
				'label-bottom-right',
				'label-top-left',
				'label-bottom-left'
			];

			function setMarkerPlacement(element, placement) {
				markerPlacementClasses.forEach(function(className) {
					element.classList.remove(className);
				});
				element.classList.add(placement);
			}

			function rectsOverlapPixels(a, b, padding) {
				return !(a.right + padding < b.left || a.left - padding > b.right || a.bottom + padding < b.top || a.top - padding > b.bottom);
			}

			function edgeOverflowScore(rect, boundary) {
				let score = 0;
				if (rect.left < boundary.left) score += (boundary.left - rect.left) * 3;
				if (rect.right > boundary.right) score += (rect.right - boundary.right) * 3;
				if (rect.top < boundary.top) score += (boundary.top - rect.top) * 3;
				if (rect.bottom > boundary.bottom) score += (rect.bottom - boundary.bottom) * 3;
				return score;
			}

			function resolveRenderedLabelCollisions() {
				if (!labelsVisible) {
					$('#markerLayer').removeClass('is-resolving');
					return;
				}

				const canvas = document.getElementById('mapCanvas');
				const markerElements = Array.from(document.querySelectorAll('.marker'));
				$('#markerLayer').addClass('is-resolving');
				const canvasRect = canvas.getBoundingClientRect();
				const boundary = {
					left: canvasRect.left + 8,
					right: canvasRect.right - 8,
					top: canvasRect.top + 96,
					bottom: canvasRect.bottom - 8
				};
				const placed = [];
				const pinBoxes = markerElements.map(function(element) {
					const pin = element.querySelector('.marker-pin');
					const rect = pin.getBoundingClientRect();
					return {
						element: element,
						rect: rect
					};
				});

				markerElements
					.sort(function(a, b) {
						return a.getBoundingClientRect().top - b.getBoundingClientRect().top;
					})
					.forEach(function(element) {
						const initialLabel = element.querySelector('.marker-label');
						if (!initialLabel || window.getComputedStyle(initialLabel).opacity === '0') {
							return;
						}

						let bestPlacement = markerPlacementClasses[0];
						let bestScore = Infinity;

						markerPlacementClasses.forEach(function(placement) {
							setMarkerPlacement(element, placement);
							const label = element.querySelector('.marker-label');
							const rect = label.getBoundingClientRect();
							let score = edgeOverflowScore(rect, boundary);

							placed.forEach(function(box) {
								if (rectsOverlapPixels(rect, box, 6)) {
									score += 120;
								}
							});

							pinBoxes.forEach(function(pinBox) {
								if (pinBox.element !== element && rectsOverlapPixels(rect, pinBox.rect, 5)) {
									score += 90;
								}
							});

							if (score < bestScore) {
								bestScore = score;
								bestPlacement = placement;
							}
						});

						setMarkerPlacement(element, bestPlacement);
						placed.push(element.querySelector('.marker-label').getBoundingClientRect());
					});
				window.requestAnimationFrame(function() {
					$('#markerLayer').removeClass('is-resolving');
				});
			}

			const calloutPlacementClasses = ['is-left', 'is-top', 'is-bottom'];

			function setCalloutPlacement(element, placement) {
				calloutPlacementClasses.forEach(function(className) {
					element.classList.remove(className);
				});
				if (placement) {
					element.classList.add(placement);
				}
			}

			function resolveRenderedCalloutPosition() {
				const callout = document.querySelector('#markerCallout .callout');
				if (!callout) {
					return;
				}

				callout.style.marginLeft = '0px';
				callout.style.marginTop = '0px';

				const canvas = document.getElementById('mapCanvas');
				const canvasRect = canvas.getBoundingClientRect();
				const boundary = {
					left: Math.max(canvasRect.left + 8, 8),
					right: Math.min(canvasRect.right - 8, window.innerWidth - 8),
					top: Math.max(canvasRect.top + 100, 8),
					bottom: Math.min(canvasRect.bottom - 8, window.innerHeight - 8)
				};
				const avoidBoxes = Array.from(document.querySelectorAll('.marker')).reduce(function(boxes, element) {
					const pin = element.querySelector('.marker-pin');
					const label = element.querySelector('.marker-label');
					if (pin) {
						boxes.push(pin.getBoundingClientRect());
					}
					if (label && window.getComputedStyle(label).opacity !== '0') {
						boxes.push(label.getBoundingClientRect());
					}
					return boxes;
				}, []);
				const placements = ['', 'is-left', 'is-top', 'is-bottom'];
				let bestPlacement = '';
				let bestScore = Infinity;

				placements.forEach(function(placement, index) {
					setCalloutPlacement(callout, placement);
					const rect = callout.getBoundingClientRect();
					let score = edgeOverflowScore(rect, boundary) + index;

					avoidBoxes.forEach(function(box) {
						if (rectsOverlapPixels(rect, box, 8)) {
							score += 140;
						}
					});

					if (score < bestScore) {
						bestScore = score;
						bestPlacement = placement;
					}
				});

				setCalloutPlacement(callout, bestPlacement);
				const finalRect = callout.getBoundingClientRect();
				let nudgeX = 0;
				let nudgeY = 0;

				if (finalRect.left < boundary.left) {
					nudgeX = boundary.left - finalRect.left;
				} else if (finalRect.right > boundary.right) {
					nudgeX = boundary.right - finalRect.right;
				}

				if (finalRect.top < boundary.top) {
					nudgeY = boundary.top - finalRect.top;
				} else if (finalRect.bottom > boundary.bottom) {
					nudgeY = boundary.bottom - finalRect.bottom;
				}

				if (nudgeX || nudgeY) {
					callout.style.marginLeft = (nudgeX / zoomLevel) + 'px';
					callout.style.marginTop = (nudgeY / zoomLevel) + 'px';
				}
			}

			function chooseCalloutSide(marker) {
				const frame = document.getElementById('markerLayer').getBoundingClientRect();
				const widthPct = Math.min(246, frame.width - 32) / frame.width * 100;
				const gateCount = Array.isArray(marker.gate_values) ? marker.gate_values.length : 0;
				const estimatedHeight = gateCount > 2 ? 215 : (gateCount > 0 ? 190 : 165);
				const heightPct = estimatedHeight / frame.height * 100;
				const gapXPct = 24 / frame.width * 100;
				const gapYPct = 24 / frame.height * 100;
				const x = Number(marker.x);
				const y = Number(marker.y);
				const markerBoxes = markers
					.filter(function(item) { return String(item.id_logger) !== String(marker.id_logger); })
					.map(function(item) {
						return {
							left: Number(item.x) - 1.4,
							right: Number(item.x) + 1.4,
							top: Number(item.y) - 2.2,
							bottom: Number(item.y) + 2.2
						};
					});
				const candidates = [
					{ side: '', left: x + gapXPct, right: x + gapXPct + widthPct, top: y - heightPct / 2, bottom: y + heightPct / 2 },
					{ side: 'is-left', left: x - gapXPct - widthPct, right: x - gapXPct, top: y - heightPct / 2, bottom: y + heightPct / 2 },
					{ side: 'is-top', left: x - widthPct / 2, right: x + widthPct / 2, top: y - gapYPct - heightPct, bottom: y - gapYPct },
					{ side: 'is-bottom', left: x - widthPct / 2, right: x + widthPct / 2, top: y + gapYPct, bottom: y + gapYPct + heightPct },
				];
				let chosen = candidates[0];
				let bestScore = Infinity;

				candidates.forEach(function(candidate) {
					let score = 0;
					if (candidate.left < 1) score += (1 - candidate.left) * 12;
					if (candidate.right > 99) score += (candidate.right - 99) * 12;
					if (candidate.top < 10) score += (10 - candidate.top) * 10;
					if (candidate.bottom > 98) score += (candidate.bottom - 98) * 10;
					markerBoxes.forEach(function(box) {
						if (boxesOverlap(candidate, box, 1.2)) {
							score += 35;
						}
					});
					if (score < bestScore) {
						bestScore = score;
						chosen = candidate;
					}
				});

				return chosen.side;
			}

			function renderMarkers(options) {
				options = options || {};
				updateMapFrame();
				const visibleMarkers = markers;
				const layouts = chooseMarkerLayouts(visibleMarkers);

				$('#markerLayer').html(visibleMarkers.map(function(marker) {
					const active = String(marker.id_logger) === String(selectedId) ? ' active' : '';
					const statusClass = markerStatusClass(marker);
					const shortStatus = shortStatusText(marker);
					const labelPlacement = layouts[String(marker.id_logger)] || 'label-right';
					const iconUrl = markerIcon(marker);
					return `
						<button class="marker ${statusClass}${active} ${labelPlacement}" type="button" data-id="${escapeHtml(marker.id_logger)}" style="--x:${Number(marker.x)};--y:${Number(marker.y)}">
							<span class="marker-pin">
								<img src="${escapeHtml(iconUrl)}" alt="" aria-hidden="true">
							</span>
							<span class="marker-label">
								<strong>${escapeHtml(marker.nama_lokasi)}</strong>
								<span>${escapeHtml(shortStatus)}</span>
							</span>
						</button>
					`;
				}).join(''));

				if (options.skipCallout) {
					$('#markerCallout').empty();
					$('#peta3dPage').removeClass('has-callout');
				} else {
					renderCallout();
				}
				window.requestAnimationFrame(function() {
					resolveRenderedLabelCollisions();
					if (!options.skipCallout) {
						window.requestAnimationFrame(resolveRenderedCalloutPosition);
					}
				});
			}

			function renderCallout() {
				const marker = markers.find(function(item) {
					return String(item.id_logger) === String(selectedId);
				});

				if (!marker) {
					$('#markerCallout').empty();
					$('#peta3dPage').removeClass('has-callout');
					return;
				}

				const statusClass = markerStatusClass(marker);
				const connectionStatus = statusClass === 'maintenance' ? 'Perbaikan' : (statusClass === 'online' ? 'Terhubung' : 'Terputus');
				const primaryValue = `${escapeHtml(marker.nilai || '-')} ${escapeHtml(marker.satuan || '')}`.trim();
				const gateValues = Array.isArray(marker.gate_values) ? marker.gate_values : [];
				const summaryContent = gateValues.length ? `
					<div class="callout-gates">
						${gateValues.map(function(gate) {
							const gateValue = `${escapeHtml(gate.nilai || '-')} ${escapeHtml(gate.satuan || 'cm')}`.trim();
							return `
								<div class="callout-gate">
									<strong>${gateValue || '-'}</strong>
									<span>${escapeHtml(gate.nama_pintu || 'Pintu')}</span>
								</div>
							`;
						}).join('')}
					</div>
				` : `
					<div class="callout-summary">
						<div>
							<span>${escapeHtml(marker.nama_parameter || 'Nilai Utama')}</span>
							<strong>${primaryValue || '-'}</strong>
						</div>
					</div>
				`;
				const calloutSide = chooseCalloutSide(marker);
				$('#markerCallout').html(`
					<div class="callout ${calloutSide}" style="--x:${Number(marker.x)};--y:${Number(marker.y)}">
						<div class="callout-head">
							<div>
								<h2 class="callout-title">${escapeHtml(marker.nama_lokasi)}</h2>
							</div>
							<button class="callout-close" type="button" aria-label="Tutup">&times;</button>
						</div>
						<div class="callout-body">
							<div class="callout-meta">
								<div class="callout-status ${statusClass}">${escapeHtml(connectionStatus)}</div>
								<div class="callout-sd">SD Card ${escapeHtml(marker.status_sd || '-')}</div>
							</div>
							${summaryContent}
							<div class="callout-time">${escapeHtml(marker.waktu || '-')}</div>
							<div class="callout-actions">
								<a class="callout-link" href="${escapeHtml(marker.link)}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="m19 9l-5 5l-4 -4l-5 5"/><path d="M14 9h5v5"/></svg>
									<span>Analisa</span>
								</a>
								<a class="callout-link" target="_blank" href="https://maps.google.com/?q=${escapeHtml(marker.latitude)},${escapeHtml(marker.longitude)}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 21l7 -18l-18 7l8 3l3 8z"/><path d="m10 14l4 -4"/></svg>
									<span>Lokasi</span>
								</a>
							</div>
						</div>
					</div>
				`);
				$('#peta3dPage').addClass('has-callout');
			}

			function updateActiveMarkers() {
				$('.marker').each(function() {
					$(this).toggleClass('active', String($(this).attr('data-id')) === String(selectedId));
				});
			}

			function clearSelectedMarker() {
				selectedId = null;
				updateActiveMarkers();
				$('#markerCallout').empty();
				$('#peta3dPage').removeClass('has-callout');
			}

			function selectMarker(id) {
				selectedId = String(id);
				updateActiveMarkers();
				renderCallout();
				window.requestAnimationFrame(resolveRenderedCalloutPosition);
			}

			function loadMarkers() {
				$.getJSON(markerUrl)
					.done(function(data) {
						if (Array.isArray(data)) {
							markers = data;
							if (!markers.some(function(marker) { return String(marker.id_logger) === String(selectedId); })) {
								selectedId = null;
							}
							renderSearchResults($('#markerSearchInput').val());
							renderMarkers();
						}
					});
			}

			function markerSearchText(marker) {
				return [
					marker.id_logger,
					marker.nama_lokasi,
					marker.kategori,
					marker.controller
				].join(' ').toLowerCase();
			}

			function searchMarkers(query) {
				const keyword = String(query || '').trim().toLowerCase();
				if (!keyword) {
					return [];
				}
				return markers.filter(function(marker) {
					return markerSearchText(marker).indexOf(keyword) !== -1;
				}).slice(0, 8);
			}

			function renderSearchResults(query) {
				const keyword = String(query || '').trim();
				const results = searchMarkers(keyword);
				const $results = $('#markerSearchResults');

				if (!keyword) {
					$results.removeClass('show').empty();
					return;
				}

				if (!results.length) {
					$results.html('<div class="search-empty">Pos tidak ditemukan</div>').addClass('show');
					return;
				}

				$results.html(results.map(function(marker) {
					return `
						<button class="search-result" type="button" data-id="${escapeHtml(marker.id_logger)}">
							<strong>${escapeHtml(marker.nama_lokasi)}</strong>
							<span>ID ${escapeHtml(marker.id_logger)}</span>
						</button>
					`;
				}).join('')).addClass('show');
			}

			$('#mapCanvas').on('pointerdown', function(event) {
				if (editMode) {
					return;
				}
				if ($(event.target).closest('.marker, .callout, .map-search, .map-tools, a, button').length) {
					return;
				}

				activePointers[event.originalEvent.pointerId] = {
					clientX: event.originalEvent.clientX,
					clientY: event.originalEvent.clientY
				};
				const pointers = activePointerList();
				this.setPointerCapture && this.setPointerCapture(event.originalEvent.pointerId);

				if (pointers.length >= 2) {
					event.preventDefault();
					isPanning = false;
					isShellPanning = false;
					$('#mapCanvas').removeClass('is-panning');
					const first = pointers[0];
					const second = pointers[1];
					pinchStart = {
						distance: pointerDistance(first, second),
						zoom: zoomLevel,
						center: pointerCenter(first, second)
					};
					return;
				}

				if (zoomLevel <= minZoom) {
					if (isMobileViewport()) {
						event.preventDefault();
						isShellPanning = true;
						shellPanStart = {
							x: event.originalEvent.clientX,
							scrollLeft: document.querySelector('.map-shell').scrollLeft
						};
					}
					return;
				}

				event.preventDefault();
				isPanning = true;
				panStart = {
					x: event.originalEvent.clientX,
					y: event.originalEvent.clientY,
					panX: panX,
					panY: panY
				};
				$('#mapCanvas').addClass('is-panning');
			});

			$('#mapCanvas').on('wheel', function(event) {
				if ($(event.target).closest('.marker, .callout, .map-search, .map-tools, a, button').length) {
					return;
				}

				event.preventDefault();
				const direction = event.originalEvent.deltaY > 0 ? -1 : 1;
				quickSetZoom(zoomLevel + (direction * zoomStep), event.originalEvent);
			});

			$(document).on('pointermove', function(event) {
				if (activePointers[event.originalEvent.pointerId]) {
					activePointers[event.originalEvent.pointerId] = {
						clientX: event.originalEvent.clientX,
						clientY: event.originalEvent.clientY
					};
				}

				const pointers = activePointerList();
				if (pinchStart && pointers.length >= 2) {
					event.preventDefault();
					const first = pointers[0];
					const second = pointers[1];
					const distance = pointerDistance(first, second);
					if (pinchStart.distance > 0) {
						const center = pointerCenter(first, second);
						quickSetZoom(pinchStart.zoom * (distance / pinchStart.distance), center);
					}
					return;
				}

				if (isShellPanning) {
					event.preventDefault();
					const shell = document.querySelector('.map-shell');
					shell.scrollLeft = shellPanStart.scrollLeft - (event.originalEvent.clientX - shellPanStart.x);
					return;
				}

				if (!isPanning) {
					return;
				}

				event.preventDefault();
				panX = panStart.panX + (event.originalEvent.clientX - panStart.x);
				panY = panStart.panY + (event.originalEvent.clientY - panStart.y);
				applyPan();
			});

			$(document).on('pointerup pointercancel', function(event) {
				delete activePointers[event.originalEvent.pointerId];
				const pointers = activePointerList();
				if (pinchStart && pointers.length < 2) {
					pinchStart = null;
					isPanning = false;
					isShellPanning = false;
					$('#mapCanvas').removeClass('is-panning');
					finishZoomInteraction();
					return;
				}

				if (isShellPanning) {
					isShellPanning = false;
					return;
				}

				if (!isPanning) {
					return;
				}

				isPanning = false;
				$('#mapCanvas').removeClass('is-panning');
				renderMarkers();
			});

			$(document).on('pointerdown', '.marker', function(event) {
				if (!editMode) {
					return;
				}

				event.preventDefault();
				draggingId = String($(this).attr('data-id'));
				this.setPointerCapture && this.setPointerCapture(event.originalEvent.pointerId);
				updateMarkerPosition(draggingId, getCanvasPosition(event.originalEvent));
			});

			$(document).on('pointermove', function(event) {
				if (!editMode || !draggingId) {
					return;
				}

				event.preventDefault();
				updateMarkerPosition(draggingId, getCanvasPosition(event.originalEvent));
			});

			$(document).on('pointerup pointercancel', function(event) {
				if (!editMode || !draggingId) {
					return;
				}

				const id = draggingId;
				const position = getCanvasPosition(event.originalEvent);
				draggingId = null;
				updateMarkerPosition(id, position);
				saveMarkerPosition(id, position);
			});

			$(document).on('click', '.marker', function(event) {
				if (editMode) {
					event.preventDefault();
					return;
				}
				selectMarker($(this).attr('data-id'));
			});

			$(document).on('click', '.callout-close', function() {
				clearSelectedMarker();
			});

			$('#markerSearchInput').on('input focus', function() {
				renderSearchResults(this.value);
			});

			$(document).on('click', '.search-result', function() {
				const id = String($(this).attr('data-id'));
				const marker = markers.find(function(item) {
					return String(item.id_logger) === id;
				});
				if (!marker) {
					return;
				}
				$('#markerSearchInput').val(marker.nama_lokasi);
				$('#markerSearchResults').removeClass('show').empty();
				focusMarker(marker);
			});

			$(document).on('click', function(event) {
				if (!$(event.target).closest('#mapSearch').length) {
					$('#markerSearchResults').removeClass('show');
				}
			});

			$('#markerSearchInput').on('keydown', function(event) {
				if (event.key !== 'Enter') {
					return;
				}
				const firstResult = searchMarkers(this.value)[0];
				if (firstResult) {
					event.preventDefault();
					$('#markerSearchInput').val(firstResult.nama_lokasi);
					$('#markerSearchResults').removeClass('show').empty();
					focusMarker(firstResult);
				}
			});

			$(document).on('keydown', function(event) {
				if (event.key === 'Escape') {
					clearSelectedMarker();
				}
			});
			$(window).on('resize', function() {
				renderMarkers();
				window.requestAnimationFrame(function() {
					applyMobileInitialView(!mobileInitialViewApplied);
				});
			});

			$('#toggleEditMarkers').on('click', function() {
				setEditMode(!editMode);
			});
			$('#toggleMarkerLabels').on('click', function() {
				setLabelsVisible(!labelsVisible);
			});
			$('#zoomOut').on('click', function() {
				smoothSetZoom(zoomLevel - zoomStep);
			});
			$('#zoomIn').on('click', function() {
				smoothSetZoom(zoomLevel + zoomStep);
			});
			$('#resetView').on('click', resetView);

			applyZoom();
			renderMarkers();
			window.requestAnimationFrame(function() {
				applyMobileInitialView(true);
			});
			window.setInterval(loadMarkers, 60000);
		</script>
	</body>
</html>
