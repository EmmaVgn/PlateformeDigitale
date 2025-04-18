
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';



import { startStimulusApp } from '@symfony/stimulus-bundle';
const app = startStimulusApp();

import TestController from './controllers/test_controller.js';
app.register('test', TestController);

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');

    toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
});

const links = document.querySelectorAll('nav ul li a');

links.forEach(link => {
    link.addEventListener('click', function() {
        links.forEach(link => link.classList.remove('active'));
        this.classList.add('active');
    });
});

document.addEventListener('DOMContentLoaded', function () {
	const scrollBtn = document.getElementById('scrollTopBtn');

	// Afficher le bouton après un scroll vers le bas
	window.addEventListener('scroll', function () {
		if (window.scrollY > 80) {
			scrollBtn.classList.remove('hidden');
		} else {
			scrollBtn.classList.add('hidden');
		}
	});

	// Scroll smooth vers le haut
	scrollBtn.addEventListener('click', function () {
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
});

