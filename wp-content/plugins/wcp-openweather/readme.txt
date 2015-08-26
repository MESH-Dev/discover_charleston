=== WCP OpenWeather ===
Contributors: webcodin
Tags:  forecast, forecast widget, local weather, openweathervap, shortcode, sidebar, weather, weather forecasts, weather widget, widgets, options, user options, plugin skin, plugin theme, Weather API, conditions, current conditions, weather by city, city name, google autocomplete, autocomplete, location, units, temperature, wind speed, pressure, multilanguage, multi language, language
Requires at least: 3.5.0
Tested up to: 4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

The weather forecast plugin based on OpenWeatherMap API that includes various sidebar widgets and shortcodes

== Description ==

WCP OpenWeather plugin allows you to add various widgets and shortcodes with current weather or forecast for your city.   

Our plugin is based on free [OpenWeatherMap API](http://openweathermap.org/) and uses free API key for weather data receiving with limitation for this plan, but you can buy any key for [paid plans](http://openweathermap.org/price/) and use it for your purposes.

In additional, plugin supports different themes, but currently is available only one default theme. We are working on creation of new themes for widgets/shortcodes, so it will be added soon. With default theme you are able to customize background/text colors for widgets/shortcodes.

Live demo you can find [here](http://wpdemo.webcodin.com/weather-forecast/).

= Latest Updates =
* Multilanguage functionality (limited languages list – will be improved soon);
* TinyMCE toolbar button for shortcode with visual settings.

= Plugin Features =

* Current weather and forecast widgets for sidebars and shortcode for page;
* Weather forecast provided by FREE OpenWeatherMap API;
* Conversion measurements and settings for temperature, wind and pressure;
* Default plugin options and personal widgets/shortcodes options for site administrator;
* Optional widget/shortcode user options for weather forecast on the site frontend for site visitors;
* Plugin themes support;
* Possibility to use OpenWeatherMap API key for free and [paid plans](http://openweathermap.org/price/);
* City name with Google Map Place Autocomplete;
* Full adaptive translation of the plugin interface including the name of the city.

= Default Theme Features =

* Widget with 2 templates: default & compact;
* Mini Widget;
* Shortcode with 2 templates: default & compact;
* Customization of background and text colors;

= Multilanguage Functionality =

First of all, we want to say **great thanks** for all people who help us with plugin translation!

Available Languages:

* English (United States) – default;
* Russian (Russia);
* Ukrainian (Ukraine);
* Albanian (Albania) – great thanks for Erjon;
* Dutch (Belgium) – great thanks for Christophe​;
* Serbian (Serbia) - great thanks for Nikola;
* Lithuanian (Lithuania) - great thanks for Mantas; 
* French (France).
 
If you want to help with plugin translation on your language please let us know via demo site [contact form](http://wpdemo.webcodin.com/stay-in-touch/) or directly via support@webcodin.com. We will send you .xlsx or .po files with necessary variables for translations.

Multilanguage functionality has limitation based on [OpenWeatherMap API](http://openweathermap.org/forecast16#multi) languages support, i.e. city name has no translation for city name by default and description of weather conditions have translation only for languages form [OpenWeatherMap](http://openweathermap.org/forecast16#multi) API list.

Currently, translation of the city names realized via Google autocomplete for city name based on active language for the plugin. 

**NB!** Translation of the city name to the active language occurs when you choose city from the Google Autocomplete list. When You change the current language then city name in the existing shortcodes and widgets will not be automatically translated to the new language. To do this, select the city again, and save the changes.

More information and documentation can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/) and [FAQ](https://wordpress.org/plugins/wcp-openweather/faq/).

= Notes =

* **NB!** Minimum required **PHP version** is **5.3.0**.
* **NB!** We use cookies to storage user options; 
* **NB!** Free OpenWeatherMap has [limitation to API request](http://openweathermap.org/price/).

== Installation ==

1. Download a copy of the plugin
2. Unzip and Upload 'wcp-openweather' to a sub directory in '/wp-content/plugins/'.
3. Activate the plugins through the 'Plugins' menu in WordPress.

= Quick current weather/forecast setup as widget =
* Check plugin "WCP OpenWeather" > "Settings" and plugin "WCP OpenWeather" > "Theme Settings" pages and customize weather forecast options for your purposes;
* Go to the "Appearance" –> "Widgets" sections;
* Add "WCP Weather" or "WCP Weather Mini" widget to necessary sidebar;
* Choose city name in the widget options, customize other options for your purposes and press "Save" button.

That is all. Widget with weather forecast will be displayed at the chosen sidebar.

= Quick current weather/forecast setup as shortcode =
* Check plugin "WCP OpenWeather" > "Settings" and plugin "WCP OpenWeather" > "Theme Settings" pages and customize weather forecast options for your purposes;
* Create new page or use existed ("Pages" > "All Pages"/"Add New");
* Add shortcode via TinyMCE toolbar button with necessary options and save the page.

That is all. Weather forecast will be displayed on the page of your site.


== Frequently Asked Questions ==

= Why I have "Parse error: syntax error, unexpected T_STRING, expecting T_CONSTANT_ENCAPSED_STRING" error after the plugin installation. =
If you have following error "Parse error: syntax error, unexpected T_STRING, expecting T_CONSTANT_ENCAPSED_STRING" after the plugin installation, please check PHP version on your server. Minimum required **PHP version** – **5.3.0**.

= Why I have "Bad Request. Your browser sent a request that this server could not understand. Size of a request header field exceeds server limit. Cookie" error? =
If you have following error "Bad Request. Your browser sent a request that this server could not understand. Size of a request header field exceeds server limit. Cookie" after the adding new widget or shortcode, please clear cookies for Your site in the browser.

= How can I add current weather/forecast to a sidebar? =
* Check plugin "WCP OpenWeather" > "Settings" and plugin "WCP OpenWeather" > "Theme Settings" pages and customize weather forecast options for your purposes;
* Go to the "Appearance" –> "Widgets" sections;
* Add "WCP Weather" or "WCP Weather Mini" widget to necessary sidebar;
* Choose city name in the widget options, customize other options for your purposes and press "Save" button.
	
More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= How can I add current weather/forecast to a page? = 
* Check plugin "WCP OpenWeather" > "Settings" and plugin "WCP OpenWeather" > "Theme Settings" pages and customize weather forecast options for your purposes;
* Create new page or use existed ("Pages" > "All Pages"/"Add New");
* Add shortcode via TinyMCE toolbar button with necessary options and save the page.
		
More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= How can I add/edit shortcode manually? = 
* Check plugin "Settings" and plugin "Theme Settings" pages and customize weather forecast options for your purposes;
* Create new page or use existed;
* Add shortcode – `[wcp_weather]` – to the TinyMCE editor and save the page. All values (location, measurement units etc.) will be used from the default plugin settings.

**NB!** If you need to add several shortcodes on a page, you will need to use following shortcode parameters, as sample `[wcp_weather id="my-weather-1" city="London, GB"]` and `[wcp_weather id="my-weather-2" city="San Francisco, US"]`.

More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= Where can I change plugin language? =
You can change plugin language here: "WCP OpenWeather" > "Settings" > "Plugin" tab > "Language" dropdown.
If you want to help with plugin translation on your language please let us know via demo site [contact form](http://wpdemo.webcodin.com/stay-in-touch/) or directly via support@webcodin.com. We will send you .xlsx or .po file with necessary variables for translations.
	
More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= Where can I change plugin theme? =
You can change plugin theme here: "WCP OpenWeather" > "Settings" > "Plugin" tab > "Active Theme" dropdown.
Currently is available only one default theme. We are working on creation of new themes for widgets/shortcodes, so it will be added soon.
	
More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= Where can I change background and text color for default theme? =
You can change default theme background text and color here: "WCP OpenWeather" > "Theme Settings".
	
More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= Where can I find default weather options? =
Default weather options you can find "WCP OpenWeather" > "Settings" > "Weather" tab.
On this page you are able to set:
* **Location** – allows to set default location name;
* **Measurement Units** – allows to set units for temperature, wind speed and pressure;
* **Display Options** – allows to set current weather/5 days forecast

More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= Where can I find default plugin options? =
Default weather options you can find "WCP OpenWeather" > "Settings" > "Plugin" tab.
On this page you are able to set:
* **General Options** – allows to set language, active theme and default weather refresh time;
* **User Options** – allows to enable/disable user option for site visitors on the site frontend and set storage time of the user options data in cookies;
* **Other** - allows to disable weather conditions description (can be used if active language does not support weather conditions translations) and change “no data” message.
	
More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

= Where can I add own API key? =
You can add own OpenWeatherMap API key here: "WCP OpenWeather" > "Settings" > "API" tab.

More information can be found in the section [screenshots](https://wordpress.org/plugins/wcp-openweather/screenshots/).

== Screenshots ==
1. Current Weather and Forecast Samples
2. Current Weather and Forecast Samples
3. Current Weather and Forecast Samples :: Mobile Version
4. Current Weather and Forecast Samples :: Mobile Version
5. User Options
6. TinyMCE actionbar Button
7. Shordcode Generator
8. Widget
9. Admin Panel :: Settings :: Weather Tab
10. Admin Panel :: Settings :: Plugin Tab
11. Admin Panel :: Settings :: API Tab
12. Admin Panel :: Theme Settings

== Changelog == 

= 1.2.4 =
* Fixed trouble with incorrect commit to ugly WordPress SVN

= 1.2.3 = 
* Added filter hook 'wcp_get_settings' that allow to modify weather attributes for getting personal weather conditions
* Added AJAX user options submit functionality
* Code optimization
* Minor changes

= 1.2.2 =
* Added translation to the Lithuanian (Lithuania) language

= 1.2.1 =
* Minor bugfixing     

= 1.2.0 =
* Added translation to the Serbian (Serbia) language
* Added ability to use different templates in the weather widgets and shortcodes
* Added new "Compact" templates for the weather widgets and shortcodes
* Added ability to use "clouds" parameter in the templates
* Changed rule of displaying humidity value. At now "0%" value is displaying as "-"

= 1.1.9 =
* Corrections for Albanian language

= 1.1.8 =
* Fixed issue with the difference between current date of the plugin and current date of the website based on a "Timezone" parameter of the Wordpress

= 1.1.7 =
* Added translation to the Dutch (Belgium) language

= 1.1.6 =
* Added translations to the Albanian and French languages

= 1.1.5 =
* Fixed issue with the error "mysqli::mysqli(): (HY000/2005): Unknown MySQL server host"

= 1.1.4 =
* Changed: Removed "uniqueId" parameter from the shortcodes generator result

= 1.1.3 =
* Minor bugfixing

= 1.1.2 =
* Fixed the issue with the error "Bad Request. Your browser sent a request that this server could not understand. Size of a request header field exceeds server limit. Cookie"

= 1.1.1 =
* Minor bugfixing

= 1.1.0 =
* Added: multilanguage support
* Added: visual shortcode generator for TinyMCE editor
* Added: additional parameters of the plugin settings
* Changed: display rules for some visual elements
* Minor changes in the style of the default theme
* Minor changes of the plugin core
* Documentation update

= 1.0.1 =
* Changed: layout of the "Settings" page
* Added: link to live demo site in description of the plugin
* Minor changes of the plugin core

= 1.0.0 =
* Initial release.
