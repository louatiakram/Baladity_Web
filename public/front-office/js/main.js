/**
 * Template Name: Logis
 * Template URL: https://bootstrapmade.com/logis-bootstrap-logistics-website-template/
 * Author: BootstrapMade.com
 * License: https://bootstrapmade.com/license/
 */
(function () {
  'use strict';

    /**
     * Preloader
     */
    const preloader = document.querySelector('#preloader');
    if (preloader) {
        window.addEventListener('load', () => {
            preloader.remove();
        });
    }

    /**
     * Sticky header on scroll
     */
    const selectHeader = document.querySelector('#header');
    if (selectHeader) {
        document.addEventListener('scroll', () => {
            window.scrollY > 100 ? selectHeader.classList.add('sticked') : selectHeader.classList.remove('sticked');
        });
    }

    /**
     * Scroll top button
     */
    const scrollTop = document.querySelector('.scroll-top');
    if (scrollTop) {
        const togglescrollTop = function () {
            window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
        }
        window.addEventListener('load', togglescrollTop);
        document.addEventListener('scroll', togglescrollTop);
        scrollTop.addEventListener('click', window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));
    }

    /**
     * Mobile nav toggle
     */
    const mobileNavShow = document.querySelector('.mobile-nav-show');
    const mobileNavHide = document.querySelector('.mobile-nav-hide');

    document.querySelectorAll('.mobile-nav-toggle').forEach(el => {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            mobileNavToogle();
        })
    });

    function mobileNavToogle() {
        document.querySelector('body').classList.toggle('mobile-nav-active');
        mobileNavShow.classList.toggle('d-none');
        mobileNavHide.classList.toggle('d-none');
    }

    /**
     * Hide mobile nav on same-page/hash links
     */
    document.querySelectorAll('#navbar a').forEach(navbarlink => {

        if (!navbarlink.hash) return;

        let section = document.querySelector(navbarlink.hash);
        if (!section) return;

        navbarlink.addEventListener('click', () => {
            if (document.querySelector('.mobile-nav-active')) {
                mobileNavToogle();
            }
        });

    });

    /**
     * Toggle mobile nav dropdowns
     */
    const navDropdowns = document.querySelectorAll('.navbar .dropdown > a');

    navDropdowns.forEach(el => {
        el.addEventListener('click', function (event) {
            if (document.querySelector('.mobile-nav-active')) {
                event.preventDefault();
                this.classList.toggle('active');
                this.nextElementSibling.classList.toggle('dropdown-active');

                let dropDownIndicator = this.querySelector('.dropdown-indicator');
                dropDownIndicator.classList.toggle('bi-chevron-up');
                dropDownIndicator.classList.toggle('bi-chevron-down');
            }
        })
    });

    /**
     * Initiate pURE cOUNTER
     */
    new PureCounter();

    /**
     * Initiate glightbox
     */
    const glightbox = GLightbox({
        selector: '.glightbox'
    });

    /**
     * Init swiper slider with 1 slide at once in desktop view
     */
    new Swiper('.slides-1', {
        speed: 600,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false
        },
        slidesPerView: 'auto',
        pagination: {
            el: '.swiper-pagination',
            type: 'bullets',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        }
    });

    /**
     * Animation on scroll function and init
     */
    function aos_init() {
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    }

    window.addEventListener('load', () => {
        aos_init();
    });

    /**
     * Drag Drop
     */
    document.addEventListener('DOMContentLoaded', function () {
      const grids = document.querySelectorAll('.grid');
      const taches = document.querySelectorAll('.tache');
  
      taches.forEach(tache => {
        tache.addEventListener('dragstart', dragStart);
        tache.addEventListener('dragend', dragEnd);
      });
  
      grids.forEach(grid => {
        grid.addEventListener('dragover', dragOver);
        grid.addEventListener('dragenter', dragEnter);
        grid.addEventListener('dragleave', dragLeave);
        grid.addEventListener('drop', dragDrop);
      });
  
      function updateCounters() {
        const todoCounter = document.getElementById('todo-counter');
        const doingCounter = document.getElementById('doing-counter');
        const doneCounter = document.getElementById('done-counter');
  
        const todoTasks = document.querySelectorAll('#TODO .tache');
        const doingTasks = document.querySelectorAll('#DOING .tache');
        const doneTasks = document.querySelectorAll('#DONE .tache');
  
        todoCounter.textContent = todoTasks.length;
        doingCounter.textContent = doingTasks.length;
        doneCounter.textContent = doneTasks.length;
      }
  
      function dragStart() {
        if (!this.closest('#DONE')) {
          this.classList.add('dragging');
        }
      }
  
      function dragEnd() {
        this.classList.remove('dragging');
      }
  
      function dragOver(e) {
        e.preventDefault();
      }
  
      function dragEnter(e) {
        e.preventDefault();
        this.classList.add('hovered');
      }
  
      function dragLeave() {
        this.classList.remove('hovered');
      }
  
      function dragDrop() {
        const tache = document.querySelector('.dragging');
        const gridId = this.id;
        const taskId = tache.id;
  
        if (gridId === 'DONE') {
          // Show confirm dialog for irreversible action
          const confirmResult = confirm(
            "This action cannot be undone. Are you sure you want to move this task to 'DONE'?"
          );
  
          if (!confirmResult) {
            // If the user cancels, exit the function
            tache.classList.remove('dragging');
            this.classList.remove('hovered');
            return;
          }
        }
  
        // Here, you can update the etat_T of the tache based on the grid's id
        // Assuming grid id format is "{etat}_grid", e.g., "todo_grid", "doing_grid", "done_grid"
        const newState = gridId.split('_')[0];
        // Example AJAX request to update etat_T
        // Replace this with your actual logic to update the tache's state
        fetch(`/update-tache-state/${taskId}/${newState}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ taskId, newState }),
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            // Handle response data if needed
            console.log('Tache state updated successfully:', data);
            // Update counters after successful update
            updateCounters();
          })
          .catch(error => {
            console.error('Error updating tache state:', error);
          });
  
        this.appendChild(tache);
        this.classList.remove('hovered');
      }
    });
  
    document.getElementById('import-csv-btn').addEventListener('click', function() {
      document.getElementById('csv-file-input').click();
  });



    /**
     * Autoresize echart charts
     */
    const mainContainer = select('#main');
    if (mainContainer) {
      setTimeout(() => {
        new ResizeObserver(function () {
          select('.echart', true).forEach(getEchart => {
            echarts.getInstanceByDom(getEchart).resize();
          });
        }).observe(mainContainer);
      }, 200);
    }
  })();