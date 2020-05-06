#Back button

A dedicated button that enables traversal backward through the navigation stack history.
Each page can host a back button that remains hidden until you navigate to that page from another, whereupon the back button becomes visible on the page.

The BackButton checks the navigation stack to determine whether the user can navigate backwards. If there is nothing to navigate back to, the button is not displayed. When the user clicks the button or uses keyboard shortcuts (such as Alt+Left or the browserBack keys), the back function is called and the previous page in the navigation stack is loaded. You don't have to write any code.