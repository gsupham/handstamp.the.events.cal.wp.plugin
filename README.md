# Live Connect - WordPress Plugin

**Bridge your event management platform with The Events Calendar plugin for rich, multimedia event experiences.**

## Overview

Live Connect transforms basic WordPress event listings into dynamic, engaging experiences by connecting your event management platform with The Events Calendar plugin. Artists create rich profiles with Spotify, YouTube, and social media content, while venues can easily select and feature performers. All content automatically syncs to WordPress, replacing standard event modals with enhanced multimedia presentations.

## 🚀 Key Features

### **Rich Artist Profiles**
- **Spotify Integration** - Showcase artist music directly in events
- **YouTube Videos** - Embedded performance videos and content
- **Social Media Links** - Instagram, Twitter, Facebook integration
- **Artist Bios & Photos** - Professional artist presentation

### **Enhanced Event Modals**
- **Replaces basic TEC modals** with multimedia-rich experiences
- **Automatic content association** - Artist content flows to events seamlessly
- **Professional presentation** - Clean, modern interface design
- **Mobile responsive** - Perfect display on all devices

### **Event Management Bridge**
- **API Integration** - Connects your event platform to WordPress
- **Automatic Syncing** - Event data flows automatically to The Events Calendar
- **Venue Management** - Easy artist selection and event setup
- **Real-time Updates** - Changes sync instantly to WordPress

### **Professional Plugin Features**
- **Automatic Updates** - GitHub-powered update system
- **WordPress 5.6+ Compatible** - Modern WordPress integration
- **The Events Calendar Integration** - Seamless TEC compatibility
- **Admin Configuration Panel** - Easy setup and management

## 📋 Requirements

- **WordPress:** 5.6 or higher
- **PHP:** 7.4 or higher
- **The Events Calendar Plugin:** Latest version
- **Your Event Management Platform API:** Access credentials

## 🛠 Installation

### **Method 1: Direct Download**
1. Download the latest release ZIP from [Releases](../../releases)
2. Upload to WordPress via **Plugins → Add New → Upload Plugin**
3. Activate the plugin
4. Configure settings in **Settings → Live Connect**

### **Method 2: Manual Installation**
1. Download and extract the plugin files
2. Upload the `live-connect` folder to `/wp-content/plugins/`
3. Activate through WordPress admin
4. Configure your API settings

## ⚙️ Configuration

1. **Navigate to Settings → Live Connect** in WordPress admin
2. **Enter your API credentials** for your event management platform
3. **Configure sync settings** (automatic/manual)
4. **Test the connection** using the built-in test tools
5. **Enable enhanced modals** for The Events Calendar

## 🔄 Automatic Updates

Live Connect includes professional automatic update functionality:

- **Automatic checks** every 12 hours for new releases
- **Manual update button** in the admin settings
- **One-click updates** just like WordPress.org plugins
- **Version notifications** appear in your WordPress admin

## 🎯 Usage

### **For Venue Owners:**
1. Install and configure Live Connect
2. Artists create profiles on your event platform
3. Select artists when creating events
4. Rich content automatically appears in WordPress

### **For Artists:**
1. Create profile on the event management platform
2. Add Spotify, YouTube, and social media links
3. Upload photos and bio information
4. Content automatically appears in venue events

### **For Website Visitors:**
1. Browse events on your WordPress site
2. Click any event to see enhanced modal
3. Enjoy rich multimedia artist content
4. Access music, videos, and social media directly

## 🏗 Technical Architecture

- **Frontend:** Enhanced JavaScript modals with multimedia support
- **Backend:** RESTful API integration with your event platform
- **Database:** Seamless integration with The Events Calendar
- **Updates:** GitHub-powered automatic update system
- **Compatibility:** WordPress 5.6+, PHP 7.4+, latest TEC

## 🔧 Developer Information

### **Hooks & Filters**
- `live_connect_api_endpoint` - Customize API endpoint
- `live_connect_modal_template` - Override modal template
- `live_connect_artist_data` - Filter artist data before display

### **Plugin Structure**
```
live-connect/
├── admin/                 # Admin interface
├── includes/             # Core plugin logic
├── public/               # Frontend functionality
└── vendor/               # Update system libraries
```

## 📞 Support

- **Issues:** [GitHub Issues](../../issues)
- **Documentation:** [Installation Guide](INSTALLATION_GUIDE.txt)
- **Product Info:** [Product Description](PRODUCT_DESCRIPTION.txt)

## 📝 License

This is a commercial WordPress plugin. License terms provided with purchase.

## 🚀 Version History

### v1.0.0
- Initial release
- Full The Events Calendar integration
- Automatic update system
- Rich artist profiles
- Enhanced event modals

---

**Transform your event listings from basic to extraordinary with Live Connect.**