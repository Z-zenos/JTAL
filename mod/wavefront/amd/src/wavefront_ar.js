// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Encapsules the behavior for creating a Wavefront 3D model in Moodle.
 *
 * Manages the UI while operations are occuring, including rendering and manipulating the model.
 *
 * @module     mod_wavefront/wavefront_ar
 * @class      wavefront_ar
 * @package    mod_wavefront
 * @copyright  2022 Ian Wild
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.9
 */

import * as THREE from 'mod_wavefront/three';
import { MTLLoader } from 'mod_wavefront/MTLLoader';
import { OBJLoader } from 'mod_wavefront/OBJLoader';
import { ARButton } from 'mod_wavefront/ARButton';
import jQuery from 'jquery';

let camera, scene, renderer;
let controller;

let reticle;
let object;
let objectscale;

let hitTestSource = null;
let hitTestSourceRequested = false;


export const init = (stage, scale) => {

	var container = document.getElementById(stage);
    console.log(container);
    
    // Object vector scaling
    objectscale = scale;
    
    // Get model files
	var mtl = jQuery(container).attr("data-mtl");
	console.log(mtl);
	var mtl_file = decodeURIComponent(mtl);
	console.log(mtl_file);
	var obj = jQuery(container).attr("data-obj");
	console.log(obj);
	var obj_file = decodeURIComponent(obj);
	console.log(obj_file);
	
	// Get camera attributes
	var cameraangle = jQuery(container).attr("data-cameraangle");
    console.log(cameraangle);
    var cameranear = jQuery(container).attr("data-cameranear");
	console.log(cameranear);
	var camerafar = jQuery(container).attr("data-camerafar");
	console.log(camerafar);
	var camerax = jQuery(container).attr("data-camerax");
	console.log(camerax);
	var cameray = jQuery(container).attr("data-cameray");
	console.log(cameray);
	var cameraz = jQuery(container).attr("data-cameraz");
	console.log(cameraz);

	scene = new THREE.Scene();

	var VIEW_ANGLE = Number(cameraangle), ASPECT = window.innerWidth / window.innerHeight, NEAR = Number(cameranear), FAR = Number(camerafar);
	camera = new THREE.PerspectiveCamera( VIEW_ANGLE, ASPECT, NEAR, FAR);
	camera.position.set(Number(camerax),Number(cameray),Number(cameraz));	

	// Lighting
	var keyLight = new THREE.DirectionalLight(new THREE.Color('hsl(30, 100%, 75%)'), 1.0);
	keyLight.position.set(-100, 0, 100);
			
	var fillLight = new THREE.DirectionalLight(new THREE.Color('hsl(240, 100%, 75%)'), 0.75);
	fillLight.position.set(100, 0, 100);
			
	var backLight = new THREE.DirectionalLight(0xffffff, 1.0);
	backLight.position.set(100, 0, -100).normalize();

	scene.add(keyLight);
	scene.add(fillLight);
	scene.add(backLight);
	
	//

	renderer = new THREE.WebGLRenderer( { antialias: true, alpha: true } );
	renderer.setPixelRatio( window.devicePixelRatio );
	renderer.setSize( window.innerWidth, window.innerHeight );
	renderer.xr.enabled = true;
	renderer.xr.setReferenceSpaceType( 'local' );
	container.appendChild( renderer.domElement );

    //
    
    renderer.xr.addEventListener('sessionend', function ( event ) {
    	scene.remove( object );
		scene.add( reticle );
		reticle.visible = false;
		controller.addEventListener( 'select', onSelect );
	});
    
	//

	document.body.appendChild( ARButton.createButton( renderer, { requiredFeatures: [ 'hit-test' ] } ) );

	//

	function onSelect() {

		if ( reticle.visible ) {

			/* Load model */
			var mtlLoader = new MTLLoader();
		    mtlLoader.load(mtl_file, (materials) => {
		
		        materials.preload();
		
		        var objLoader = new OBJLoader();
		        objLoader.setMaterials(materials);
		        objLoader.load(obj_file, function (obj) {
		            object = obj;
		        	reticle.matrix.decompose( object.position,  object.quaternion, object.scale );
		        	object.scale.set(objectscale,objectscale,objectscale);
					scene.add( object );
					controller.removeEventListener( 'select', onSelect );
					scene.remove(reticle);
				});
			});
		}
	}

	controller = renderer.xr.getController( 0 );
	controller.addEventListener( 'select', onSelect );
	scene.add( controller );

	reticle = new THREE.Mesh(
		new THREE.RingGeometry( 0.15, 0.2, 32 ).rotateX( - Math.PI / 2 ),
		new THREE.MeshBasicMaterial()
	);
	reticle.matrixAutoUpdate = false;
	reticle.visible = false;
	scene.add( reticle );

	//

	window.addEventListener( 'resize', onWindowResize );

    animate();
}

function onWindowResize() {

	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();

	renderer.setSize( window.innerWidth, window.innerHeight );

}

//

function animate() {

	renderer.setAnimationLoop( render );

}

function render( timestamp, frame ) {

	if ( frame ) {

		const referenceSpace = renderer.xr.getReferenceSpace();
		const session = renderer.xr.getSession();

		if ( hitTestSourceRequested === false ) {

			session.requestReferenceSpace( 'viewer' ).then( function ( referenceSpace ) {

				session.requestHitTestSource( { space: referenceSpace } ).then( function ( source ) {

					hitTestSource = source;

				} );

			} );

			session.addEventListener( 'end', function () {

				hitTestSourceRequested = false;
				hitTestSource = null;

			} );

			hitTestSourceRequested = true;

		}

		if ( hitTestSource ) {

			const hitTestResults = frame.getHitTestResults( hitTestSource );

			if ( hitTestResults.length ) {

				const hit = hitTestResults[ 0 ];

				reticle.visible = true;
				reticle.matrix.fromArray( hit.getPose( referenceSpace ).transform.matrix );

			} else {

				reticle.visible = false;

			}

		}

	}

	renderer.render( scene, camera );

}