# VT220 Character Set Editor

## Info
This is a HTML/JS/PHP application, that allows to define a custom character sets on a DEC VT220 terminal. This allows to create custom fonts or simple grahics.

## Instructions
- Configure your serial port and its baud rate in terminal.php
- Start a local PHP server with "php -S 127.0.0.1:8000"
- Open a browser and go to "http://localhost:8000/"
- Resize your screen to make sure you see all three parts: character set, screen layout and terminal preview
- Left click on a character grid to edit the character bitmap
- Right click on character to select it for assignment on screen layout
- Left click on screen layout to place the selected character
- Right click on screen layout to unassign characters
- To make the VT220 display the custom character set, hit "Send Custom Character Set", "Switch To Custom Character Mode", "Send Screen Layout"
- Use "Load Character Set" and "Load Screen Layout" to load previously saved character sets and screen layouts. Check the "saved" folder for example files
- You can create your own files, by simply copying the content in the text areas to according .txt files.

I tested it on macOS Catalina and Ubuntu with PHP 7 and Firefox.
