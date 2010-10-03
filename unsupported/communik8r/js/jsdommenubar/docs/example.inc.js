function createjsDOMenu() {
  mainMenu1 = new jsDOMenu(130);
  with (mainMenu1) {
    addMenuItem(new menuItem("Item 1", "", "example.htm"));
    addMenuItem(new menuItem("Item 2", "", "example.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 3", "", "example.htm"));
    addMenuItem(new menuItem("Item 4", "", "example.htm"));
  }
  
  mainMenu2 = new jsDOMenu(150);
  with (mainMenu2) {
    addMenuItem(new menuItem("Item 1", "", "example.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 2", "", "example.htm"));
    addMenuItem(new menuItem("Item 3", "", "example.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 4", "", "example.htm"));
  }
  
  menuBar = new jsDOMenuBar();
  with (menuBar) {
    addMenuBarItem(new menuBarItem("Item 1", mainMenu1));
    addMenuBarItem(new menuBarItem("Item 2", mainMenu2));
  }
  menuBar.moveTo(10, 10);
}