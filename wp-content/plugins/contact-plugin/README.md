

https://docs.carbonfields.net/quickstart.html




<!-- 00:00 Start
00:20 Basic Introduction
02:40 Plugin Folder setup
03:20 Securing your plugin
==> tạo thêm file index để tránh bị lỗ hỏng bảo mật -->

<!-- 08:17 Create your Plugin Class -->

<!-- 09:28 Installing Carbon Fields into Plugin via Composer 
viết câu lệnh này để cài đặt thư viện
Trong README hướng dẫn chạy composer install sau khi clone
CI/CD pipeline cần chạy composer install khi deploy -->
composer require htmlburger/carbon-fields

<!-- 13:19 Set up Composer Autoload & Constant for Plugin Path -->

<!-- 15:28 Instantiate our Class -->

<!-- 16:05 Breaking our Plugin into Separate Files for Simplicity -->

<!-- 18:20 Setting up Carbon Fields for our Custom Plugin Options -->

<!-- 34:45 Creating a Shortcode for our Contact Form -->

<!-- 38:16 Create Form Template for Front End Display -->

<!-- 41:43 Set up REST Endpoint to Receive Post Requests -->

<!-- 44:34 Set up AJAX to Point to our REST Endpoint -->

<!-- 51:13 Handle Form Data (Nonce check, Send Email) -->

<!-- 1:12:58 Displaying Submissions in the WordPress Admin (Custom Post Type) -->

<!-- 1:27:53 Set Up Meta Box to Display Submission Data -->

<!-- 1:45:10 Creating Custom Columns on Submission Post Type -->

<!-- 1:54:07 Enable Searching Custom Meta Data in Submission Table -->

<!-- 1:58:55 Enqueue CSS for Contact Form -->

<!-- 2:05:15 Sanitising Form Data -->

<!-- 2:15:20 Make Plugin Options Work in Our Plugin -->

<!-- 2:24:24 Sort Plugin Menu Order -->

<!-- 2:26:03 Make Submissions Only Accessible By Admins -->

<!-- 2:27:01 Summary & Ending -->
