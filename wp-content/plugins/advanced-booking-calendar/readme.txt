=== Advanced Booking Calendar ===
Contributors: BookingCalendar
Tags: Accommodation, Accommodations, Availability Calendar, B&B, Bed & Breakfast, Bed and Breakfast, Belegungsplan, Booking, Booking Calendar, Booking Engine, Booking System, Booking System Plugin, Buchungskalendar, Ferienwohnung, Hotel, Hotel Booking, Hotel Booking Software, Hotel Management Online Booking, Hotels, Motel Booking, Online Hotel Software, Reservation, Reservation Plugin, Reservation System, Room Availability, Rooms, Villa Booking, WordPress Hotel Booking Plugin
Requires at least: 4.2.0
Tested up to: 4.5.3
Stable tag: 1.2.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Booking System that makes managing your online reservations easy. A great Booking Calendar plugin for Accommodations.

== Description ==
The easy way to manage your bookings and raise your occupancy rate. This plugin is made for modern Hoteliers who want to get hold of their online reservations.

= Booking System =
* Fully **responsive**, backend and frontend. Check your availabilities on your phone.
* Bookings stored in **your WordPress database**
* Booking will **generate an email** where you can accept or reject bookings
* Guests will receive emails when their **reservation** is generated, confirmed or rejected
* **Easy to manage prices** by seasons and room types
* All reservations are **easy to access and manage** in the backend.
* Change the **minimum number of nights** a guest has to stay for different seasons.

= Booking Calendar =
* Calendar **overview** for all your active rooms in your Hotel
* Single **calendar for each room type**
* Create calendars for up to 15 rooms
* Every calendar is responsive and **works great on mobile**

= Booking Form =
* Responsive form that **searches for matching rooms** by date and guest count
* Inputs can be stored in **Cookies**
* Generates one reservation per booking

= Analytics =
* Analytic function helps you to identify high seasons
* Find the best pricings for your Hotel
* See how many requests fail and what the average person count is

= Google Universal Analytics =
* Integrates with your own **Google Universal Analytics** account
* User steps in the booking form are tracked and can be used to define a target 
* Every action by the user on the **booking calendar is tracked** and helps you to identify new potentials

= Even more features = 
* Availability check **widget** 
* **Email templates** for each email
* **Cookies** can be enabled to store user inputs
* Select email address to receive booking notifications
* Switch between showing the currency sign before or after the price.
* Ready for **translation**
* Comes with **German**, **Dutch**, **French**, **Italian**, **Russian** and **Portuguese** translations

Any questions? Contact us at info@booking-calendar-plugin.com or https://twitter.com/BookingCal
Or visit us at https://www.booking-calendar-plugin.com

== Installation ==

For a complete setup guide take a look at: https://booking-calendar-plugin.com/setup-guide/

1. Install and activate the plugin through your WordPress admin.
2. Check the settings on the "Advanced Booking Calendar" settings page.
3. Create calendars, rooms and seasons.
4. Add shortcodes and widget.

= Shortcodes =
This plugin uses four different shortcodes you can put on your pages. We recommend to use one page for each shortcode.

* Calendar Overview / **[abc-overview]**
The shortcode [abc-overview] shows all calendars and there availabilities by month.
* Single Calendar / **[abc-single calendar=X]**
The shortcode [abc-single calendar=X] needs a calendar id instead of X. You can find the ids on your calendar settings page. Example [abc-single calendar=1]
* Booking form / **[abc-bookingform]**
The booking form fulfils two tasks: finding the right room for users and generating booking requests. Every user action happens onpage via AJAX, so the page does not reload during interactions. We recommend to enter the shortcode [abc-bookingform] on a single page.

= Widget =
You can also use an availability check widget. Just go to “Appearance / Widgets” and add the widget.

== Screenshots ==

1. **Frontend overview** for all room types
2. **Single calendar** with date selection and "Book Now"-button
3. Booking form **without page reloads** and easy booking
4. **Backend overview** of all confirmed reservations
5. Managing bookings in the backend
6. Easiest way to **manage prices** and calendars
7. **Analytic features** help you to find high request times
8. Various settings to localize this plugin
9. Placeholdes and **email templates**

== Changelog ==

= 1.2.4 = 
* Changed the example date in the settings to 2016-12-15, to make it easier to figure out what days and months are.
* Changed the minimum price of an extra to 0.01.
* Fixed a minor bug when editing existing extras (thanks to Afinfo).
* Fixed a bug when calculating extra prices for confirmation mails (thanks to Bizbees).
* Fixed a formatting bug for prices. All prices shown are now formatted correctly.
* Fixed a bug in the price calculation when there were only optional extras.
* Improved the layout on the booking form confirmation page.

= 1.2.3 = 
* Added the possibility for admins to enter bookings with checkin/checkout-dates in the past.
* Improved the calendar legend. "Partly booked" now only shows when there are two or more rooms for the selected calendar id (or in total for the calendar overview).
* Fixed a minor bug when sending mails. The extras were not shown, when confirming a booking (thanks to Johan).
* Fixed a minor bug when adding a new extra.

= 1.2.2 =
* Updated Dutch translation (thanks to Johan). 
* Fixed a minor bug when sending mails. The extras were not shown, when there was only one (thanks to Johan). 

= 1.2.1 =
* Fixed a minor bug with the booking form layout. 

= 1.2.0 =
* Added new feature called "extras". You can now add optional or mandatory extras like "final cleaning" or costs for additional towels. Just select the price and its calculation (day/night/person etc.) and the extra will automatically show up in the booking form.
* Added the possibility to change the address fields in the booking form. Select between the options "required", "optional" or "disabled". If you change the address fields, please make sure to update your email templates. 
* Fixed a bug in the widget.
* Fixed a bug in the single calendar when cookies are enabled.
* Fixed a bug in the booking form for the Internet Explorer.

= 1.1.10 =
* Added support for PHP versions < 5.3.
* Fixed a typo.

= 1.1.9 =
* You can now show a legend explaining the colors on the calendars. Just add "legend=1" to a shortcode add, i.e. "[abc-single calendar=1 legend=1]". Works for single and overview calendars.
* Control when a day is shown as "partly booked" in the calendars. Just edit your existing calendars and enter the threshold of number of bookings for every calendar when to show "partly booked".

= 1.1.8 =
* Now able to configure the minimum stay. Enter the number of nights for each calendar or season.
* Rejected and canceled bookings can now get deleted.
* Fixed minor CSS issues.

= 1.1.7 =
* The drop down with the number of persons in the booking form now shows the highest number of guests for a single room.
* Error messages in the booking form are now smaller and the empty form fields now have a red border.
* Calendars with only one room are now a single row in the availability overview in the backend.
* Updated Portuguese language pack.

= 1.1.6 =
* Added translations for jQuery Datepicker
* Fixed 'Change room' function

= 1.1.5 =
* Added translations for jQuery Validition
* Some minor bug fixes.

= 1.1.4 =
* Added a widget for an availability check. Users can select dates and quickly start the booking form. 
* Added a Portuguese language pack (thanks to Miguel!)
* After selecting a date on the single calendar and clicking on "book now", the booking form loads automatically now. 
* Fixed missing translations in single calendar.
* Fixed a bug in the price calculation for seasons (thanks to Michael and Leslie).
* Changed notices when adding a calendar in the backend.
* Calendars with existing bookings can't be deleted anymore. 

= 1.1.3 =
* Changed query for table creation.
* Fixed a bug for using cookies.
* Old cookie values do not get deleted, but ignored.

= 1.1.2 =
* Fixed a bug when editing the rooms of a calendar.
* Fixed a bug for certain MySQL versions.

= 1.1.1 =
* Added a setup checklist.
* Added notices when changes happen on Season & Calendar setting page.
* Old cookie values are now getting deleted.
* Fixed a backend problem with the date format 'd/m/Y'.
* Fixed a bug where the confirmation email did not work.
* Fixed a bug on the analytics page when there were no calendars yet.

= 1.1 =
* New setting to change the position of the currency sign (before or after the amount).
* New setting to add tiny powered-by-link below the Calendar Overview.
* New backend module to collect feedback by user.
* Storing plugin version number from now on.
* Fixed an error for date format 'd/m/Y' in the booking form.
* Added CSS to make Calendar Overview and Booking Form look better in some themes.
* Changed ID to shortcode in the WP-admin calendar table.
* Changed button in Booking Form from "Book now" to "Select room".

= 1.0.2 =
* Fixed error that made bookings in the past possible.
* Checkout was called checkin on the single calendar when cookies were disabled.

= 1.0.1 =
* Fixed translations

= 1.0 =
* Initial release