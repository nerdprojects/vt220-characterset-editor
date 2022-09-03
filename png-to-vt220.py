#!/usr/bin/env python3

from PIL import Image
import sys

if len(sys.argv) < 2:
  print("provide png-8-gray image")
  sys.exit(-1)

image = Image.open(sys.argv[1])

width, height = image.size
if (width % 8 != 0):
  print("image width must be dividable by 8")
  sys.exit(-1)

if (height % 10 != 0):
  print("image height must be dividable by 10")
  sys.exit(-1)

pixels = image.load()
characters = []
characters.append(80*'0')
# initialize two dimensional list
layout = [[0 for x in range(80)] for y in range(24)] 

character_id = 2

y_start = 0
while y_start < height:
  x_start = 0
  while x_start < width:

    character = ''
    for y in range(10): 
      for x in range(8):
        if(pixels[x_start + x, y_start + y] == 0):
          character += '1'
        else:
          character += '0'

    # prevent duplicate characters
    try:
      existing_id = characters.index(character)
      layout[y_start//10][x_start//8] = existing_id+1
    except ValueError:
      characters.append(character)
      layout[y_start//10][x_start//8] = character_id
      character_id += 1

    x_start += 8
  
  y_start += 10

print('generated ' + str(len(characters)) + ' tiles, 94 fit into the custom character set')
print('------- Character Set ---------')
for character in characters:
  print(character, end='')
print('')
print('------- Screen Layout ---------')
for row in layout:
  for column in row:
    print(chr(column+0x20), end='')
  print('')
print('-------------------------------')
