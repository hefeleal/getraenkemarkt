# Getränkemarkt
This is a web app that I wrote for the in-house beverage market of my former student dorm. It can be used to easily calculate the total cost of single bottles of drinks, full or partially full crates, or any combination of these. Additionally, the application can be used to subtract the value of any returned deposit from the total cost.

There is a separate page that displays a log of all purchases, as well as some statistics to see how different common kitchens of the dorm compare with each other.

The user interface is only available in German, but the code and comments are in English.

## Prerequisites
You only need PHP ≥ 5.5.0 installed. No external libraries are used. For storing data, we only use csv files, so no database is needed either.

## Setup
Before you can use the app, you need to set it up with two files in the `data` folder:

1. `config.ini`

   In this file, you have to set the hashed and salted password of the application, as well as a random cookie secret. The password can be generated using the `generate_password_hash($clear_text)` function in `helpers.php`. To generate a random String for the cookie secret, the Linux command `pwgen -s 50` can be used.

   Additionally, you have to enter the names of the common kitchens in the dorm, so that accurate statistics can be generated. You just separate the names with a semicolon in the `kitchens` variable of the config file.

   Optionally, you can also set the background color of the website here.

2. `products.csv`

   This file lists all beverages that are sold. Every entry consists of an ID, a date period from when until when it is available, the name of the item, the price of one crate (including its deposit), the price of one bottle (including its deposit), the deposit of one full crate, the deposit of one bottle, the deposit for an empty crate, how many bottles there are per crate, and the type of the beverage. The type can be "bier" (beer), "radler" (shandy), or "rest" (other). These three different types of drinks are registered separately in the statistics. Tip: if you have long beverage names that destroy the layout of the app on mobile, you can suggest the browser to split the word with a hyphen by using the HTML code `&shy;` in the name.

   The dates must be in the format "DD.MM.YYYY hh:mm:ss". The IDs are in the format "x.y". For the very fist beverage, x and y are both set to 0. If a new beverage is added to the list, the highest x is incremented by 1 and y is set to 0 for that ID. If something about a beverage changes (e.g. the price or the deposit), a new entry must be inserted directly underneath the old one with the same x-ID, but with the y-ID incremented by 1. The end date of the old entry and the start date of the new entry should both be set to the point in time when the change takes effect. This approach makes it possible to track changes over time without any information being lost. The end date field of a beverage can be left empty if there isn't any.

   Lastly, you need to add images of the drinks in the `/img/drinks` folder. Every image is supposed to picture label of the bottle or the bottle itself. The beverage with ID `x.y` must have an image named `x.jpg`. All images should be the same size.

Both files in this repository contain some sample values.

## Usage

First, enter how much deposit the customer brought back. There are full crates, empty crates, and single bottles. The number for bottles can be negative, which is useful if e.g. one full crate is returned, but there are two bottles missing. You would enter that as 1 full crate and -2 bottles.

Next, you enter how many full crates of each beverage are bought using the button selections. If single bottles are bought, you can specify that using the text field next to the buttons. Similar to the deposit, a negative amount of bottles can be entered, e.g. if someone buys a crate which is only half full.

Finally, you have to enter the common kitchen, that this purchase is assigned to and submit the form. You will be prompted to enter the password, which can be stored as an encrypted cookie on the device.

On the page `uebersicht.php`, you find a log of all purchases and some statistics for the common kitchens.

## Impact

Over the course of 6 semesters, there have been more than 1,700 registered purchases using this application. More than 4,800 crates have been sold with over 65,000€ being spent and 15,000€ of deposit being returned.
