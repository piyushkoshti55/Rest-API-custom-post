let currentPage = 1;
const booksPerPage = 5;
const totalPages = 4 ;


        function updatePaginationButtons(totalPages) {
            document.getElementById('prev-page').disabled = currentPage === 1;
            document.getElementById('next-page').disabled = currentPage === totalPages;
        }
const apiUrl = 'https://demolocal.local/wp-json/custom/v1/books?per_page=${booksPerPage}&page=${currentPage}';

        async function fetchBooks() {
            try {
                const response = await fetch(`${apiUrl}?per_page=${booksPerPage}&page=${currentPage}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                // const totalPages = response.headers.get('X-WP-TotalPages');
                const books = await response.json();
                console.log(totalPages);
                displayBooks(books);
                updatePaginationButtons(totalPages);
            } catch (error) {
                console.error('Error fetching books:', error);
            }
        }

        function displayBooks(books) {
            const bookList = document.getElementById('book-list');
            bookList.innerHTML = '';
            books.forEach(book => {
                const bookItem = document.createElement('div');
                //console.log(book);
                bookItem.innerHTML = `<a href='${book.link}'><h2>${book.title}</h2></a><p>${book.excerpt}</p>`;
                bookList.appendChild(bookItem);
            });
        }

        
        fetchBooks();

        document.getElementById('next-page').addEventListener('click', () => {
            currentPage++;
            fetchBooks(currentPage);
        });
        
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchBooks(currentPage);
            }
        });
        
// AJAX Book Filter 
jQuery(document).ready(function($) {
    jQuery('#book_filter_form').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Gather the form data
        var formData = jQuery(this).serialize();

        // Send the AJAX request
        $.ajax({
            url: ajaxfilter.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_books',
                security: ajaxfilter.security, // Replace with actual nonce from PHP
                publisher: jQuery('#publisher').val(),
                posts_per_page: jQuery('[name="posts_per_page"]').val()
            },
            success: function(response) {
                if (response.success) {
                    jQuery('#results').html(response.data); // Update with results
                } else {
                    jQuery('#results').html('No books found!'); // Handle no results
                }
            },
            error: function() {
                jQuery('#results').html('An error occurred. Please try again.'); // Handle errors
            }
        });
    });
});


