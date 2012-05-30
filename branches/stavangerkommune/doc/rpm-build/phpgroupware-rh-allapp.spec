%define packagename phpGroupWare-all-apps
%define phpgwdirname phpgroupware
%define version 0.9.16.001
%define packaging 1
%define httpdroot  /var/www/html

Summary: phpGroupWare is a web-based groupware suite written in php. 
Name: %{packagename}
Version: %{version}
Release: %{packaging}
Copyright: GPL
Group: Web/Database
URL: http://www.phpgroupware.org/
Source: phpgroupware-%{version}.tar.bz2
BuildRoot: /tmp/%{packagename}-buildroot
Prefix: %{httpdroot}
Vendor: phpGroupWare
Packager: phpGroupWare <rpm@phpgroupware.org>
Buildarch: noarch
AutoReqProv: no
Requires: php >= 4.1.0
%description
phpGroupWare is a web-based groupware suite written in PHP. This package provides:

phpgroupware core app, addressbook, bookmark, calendar, chat, chora (view cvs repository), comic, developer_tools, dj, doc, eldaptir, email, felamimail, filemanager, folders, forum, ftp, fudforum, headlines hr (human resources), 
img (image editor), infolog (CRM), javassh, manual, messenger (internel message app), news_admin, nntp, notes, phonelog, phpbrain, phpsysinfo, polls, projects (advanced project management), qmailldap, registration, sitemgr (web content manager), skel, soap, stocks, todo, tts, wiki, xmlrpc. 

It also provides an API for developing additional applications. See the phpgroupware
apps project for add-on apps.

%prep
%setup -n %{phpgwdirname}

%build
# no build required

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{prefix}/%{phpgwdirname}
cp -aRf * $RPM_BUILD_ROOT%{prefix}/%{phpgwdirname}
#mkdir -p $RPM_BUILD_ROOT%{prefix}/%{phpgwdirname}/files/home
#mkdir -p $RPM_BUILD_ROOT%{prefix}/%{phpgwdirname}/files/groups
#mkdir -p $RPM_BUILD_ROOT%{prefix}/%{phpgwdirname}/files/users


%clean
rm -rf $RPM_BUILD_ROOT

%post

%postun

%files
#%attr(0770,apache,apache) %{prefix}/%{phpgwdirname}/files/groups
#%attr(0770,apache,apache) %{prefix}/%{phpgwdirname}/files/users
#%attr(0770,apache,apache) %{prefix}/%{phpgwdirname}/files/home
%defattr(-,root,root)
%dir %{prefix}/%{phpgwdirname}
%{prefix}/%{phpgwdirname}/home.php
%{prefix}/%{phpgwdirname}/about.php
%{prefix}/%{phpgwdirname}/anon_wrapper.php
%{prefix}/%{phpgwdirname}/notify.php
%{prefix}/%{phpgwdirname}/notify_simple.php
%{prefix}/%{phpgwdirname}/redirect.php
%{prefix}/%{phpgwdirname}/set_box.php
%{prefix}/%{phpgwdirname}/header.inc.php.template
%{prefix}/%{phpgwdirname}/version.inc.php
%{prefix}/%{phpgwdirname}/index.php
%{prefix}/%{phpgwdirname}/login.php
%{prefix}/%{phpgwdirname}/logout.php
%{prefix}/%{phpgwdirname}/CVS
%{prefix}/%{phpgwdirname}/doc
%{prefix}/%{phpgwdirname}/phpgwapi
%{prefix}/%{phpgwdirname}/admin
%{prefix}/%{phpgwdirname}/preferences
%{prefix}/%{phpgwdirname}/setup
#%{prefix}/%{phpgwdirname}/files
%{prefix}/%{phpgwdirname}/addressbook
%{prefix}/%{phpgwdirname}/bookmarks
%{prefix}/%{phpgwdirname}/calendar
%{prefix}/%{phpgwdirname}/chat
%{prefix}/%{phpgwdirname}/chora
%{prefix}/%{phpgwdirname}/comic
%{prefix}/%{phpgwdirname}/developer_tools
%{prefix}/%{phpgwdirname}/dj
%{prefix}/%{phpgwdirname}/eldaptir
%{prefix}/%{phpgwdirname}/email
%{prefix}/%{phpgwdirname}/etemplate
%{prefix}/%{phpgwdirname}/felamimail
%{prefix}/%{phpgwdirname}/filemanager
%{prefix}/%{phpgwdirname}/folders
%{prefix}/%{phpgwdirname}/forum
%{prefix}/%{phpgwdirname}/ftp
%{prefix}/%{phpgwdirname}/fudforum
%{prefix}/%{phpgwdirname}/headlines
%{prefix}/%{phpgwdirname}/hr
%{prefix}/%{phpgwdirname}/img
%{prefix}/%{phpgwdirname}/infolog
%{prefix}/%{phpgwdirname}/javassh
%{prefix}/%{phpgwdirname}/manual
%{prefix}/%{phpgwdirname}/messenger
%{prefix}/%{phpgwdirname}/news_admin
%{prefix}/%{phpgwdirname}/nntp
%{prefix}/%{phpgwdirname}/notes
%{prefix}/%{phpgwdirname}/phpbrain
%{prefix}/%{phpgwdirname}/phonelog
%{prefix}/%{phpgwdirname}/phpsysinfo
%{prefix}/%{phpgwdirname}/polls
%{prefix}/%{phpgwdirname}/projects
%{prefix}/%{phpgwdirname}/property
%{prefix}/%{phpgwdirname}/qmailldap
%{prefix}/%{phpgwdirname}/registration
%{prefix}/%{phpgwdirname}/sitemgr
%{prefix}/%{phpgwdirname}/skel
%{prefix}/%{phpgwdirname}/soap.php
%{prefix}/%{phpgwdirname}/soap
%{prefix}/%{phpgwdirname}/stocks
%{prefix}/%{phpgwdirname}/todo
%{prefix}/%{phpgwdirname}/tts
%{prefix}/%{phpgwdirname}/wiki
%{prefix}/%{phpgwdirname}/xmlrpc.php
%{prefix}/%{phpgwdirname}/xmlrpc

%changelog
* Thu Jul 1 2004 phpGroupWare <rpm@phpgroupware.org> 0.9.16.001
- Nightly CVS Builds

* Fri Mar 12 2004 Chris Weiss <chris@free-source.com> 0.9.16.000
- First 0.9.16 RPM build.
- See http://phpgroupware.org for complete list of new features.

# end of file
