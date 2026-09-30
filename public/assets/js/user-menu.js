// Opens and closes the user menu in the header.
// Without JavaScript the menu simply stays open, so it is always usable.

const menuButton = document.querySelector('.js-user-menu-button');
const menuList = document.getElementById('user-menu-list');

if (menuButton && menuList) {
    const openMenu = () => {
        menuList.hidden = false;
        menuButton.setAttribute('aria-expanded', 'true');
    };

    const closeMenu = () => {
        menuList.hidden = true;
        menuButton.setAttribute('aria-expanded', 'false');
    };

    // Start closed
    closeMenu();

    // Click on the button: toggle
    menuButton.addEventListener('click', () => {
        if (menuList.hidden) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    // Escape: close and put focus back on the button
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menuList.hidden) {
            closeMenu();
            menuButton.focus();
        }
    });

    // Click anywhere outside the menu: close
    document.addEventListener('click', (event) => {
        const clickedInside = menuButton.contains(event.target) || menuList.contains(event.target);
        if (!clickedInside) {
            closeMenu();
        }
    });
}
