<?xml version="1.0" encoding="UTF-8"?>
  <!--  $Id: phpsysinfo.xslt 229 2009-06-05 07:50:12Z bigmichi1 $ -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fn="http://www.w3.org/2005/xpath-functions" xmlns:xdt="http://www.w3.org/2005/xpath-datatypes"
  xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <xsl:output version="4.0" method="html" indent="no" encoding="UTF-8"
    doctype-public="-//W3C//DTD HTML 4.0 Transitional//EN" doctype-system="http://www.w3.org/TR/html4/loose.dtd" />
  <xsl:param name="SV_OutputFormat" select="'HTML'" />
  <xsl:variable name="XML" select="/" />
  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:text>phpSysInfo STATIC</xsl:text>
        </title>
        <style type="text/css">
          <xsl:comment>
            @import url("templates/phpsysinfo.css");
          </xsl:comment>
        </style>
      </head>
      <body>
        <xsl:for-each select="$XML">
          <xsl:for-each select="phpsysinfo">
            <div>
              <xsl:for-each select="Vitals">
                <h1 id="title">
                  <span>
                    <xsl:text>System Information: </xsl:text>
                  </span>
                  <xsl:for-each select="Hostname">
                    <xsl:apply-templates />
                  </xsl:for-each>
                  <span>
                    <xsl:text> (</xsl:text>
                  </span>
                  <xsl:for-each select="IPAddr">
                    <xsl:apply-templates />
                  </xsl:for-each>
                  <span>
                    <xsl:text>)</xsl:text>
                  </span>
                </h1>
              </xsl:for-each>
              <div id="vitals">
                <xsl:for-each select="Vitals">
                  <h2>
                    <span>
                      <xsl:text>System Vital</xsl:text>
                    </span>
                  </h2>
                  <table border="0" cellspacing="0" class="stripMe" id="vitalsTable"
                    width="100%">
                    <tbody>
                      <tr>
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Canonical Hostname</xsl:text>
                          </span>
                        </td>
                        <td>
                          <xsl:for-each select="Hostname">
                            <xsl:apply-templates />
                          </xsl:for-each>
                        </td>
                      </tr>
                      <tr class="odd">
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Listening IP</xsl:text>
                          </span>
                        </td>
                        <td>
                          <xsl:for-each select="IPAddr">
                            <xsl:apply-templates />
                          </xsl:for-each>
                        </td>
                      </tr>
                      <tr>
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Kernel Version</xsl:text>
                          </span>
                        </td>
                        <td>
                          <xsl:for-each select="Kernel">
                            <xsl:apply-templates />
                          </xsl:for-each>
                        </td>
                      </tr>
                      <tr class="odd">
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Distro Name</xsl:text>
                          </span>
                        </td>
                        <td>
                          <xsl:for-each select="Distroicon">
                            <img style="height:16px; width:16px; ">
                              <xsl:attribute name="src">
																<xsl:if
                                test="substring(string(concat(&apos;gfx/images/&apos;,.)), 2, 1) = ':'">
																	<xsl:text>file:///</xsl:text>
																</xsl:if>
																<xsl:value-of
                                select="translate(string(concat(&apos;gfx/images/&apos;,.)), '&#x5c;', '/')" />
															</xsl:attribute>
                              <xsl:attribute name="alt" />
                            </img>
                          </xsl:for-each>
                          <span>
                            <xsl:text>&#160;</xsl:text>
                          </span>
                          <xsl:for-each select="Distro">
                            <xsl:apply-templates />
                          </xsl:for-each>
                        </td>
                      </tr>
                      <tr>
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Uptime</xsl:text>
                          </span>
                        </td>
                        <td>
                          <span>
                            <xsl:value-of select="floor( Uptime div 60 div 60 div 24)" />
                          </span>
                          <span>
                            <xsl:text> Days </xsl:text>
                          </span>
                          <span>
                            <xsl:value-of
                              select="floor( ( Uptime div 60 div 60) - ( floor( Uptime div 60 div 60 div 24) * 24) )" />
                          </span>
                          <span>
                            <xsl:text> Hours </xsl:text>
                          </span>
                          <span>
                            <xsl:value-of
                              select="floor( Uptime div 60 - ( floor( Uptime div 60 div 60 div 24) * 60 * 24) - ( floor( ( Uptime div 60 div 60) - ( floor( Uptime div 60 div 60 div 24) * 24) ) * 60) )" />
                          </span>
                          <span>
                            <xsl:text> Minutes</xsl:text>
                          </span>
                        </td>
                      </tr>
                      <tr class="odd">
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Current Users</xsl:text>
                          </span>
                        </td>
                        <td>
                          <xsl:for-each select="Users">
                            <xsl:apply-templates />
                          </xsl:for-each>
                        </td>
                      </tr>
                      <tr>
                        <td style="width:160px; ">
                          <span>
                            <xsl:text>Load Averages</xsl:text>
                          </span>
                        </td>
                        <td>
                          <xsl:for-each select="LoadAvg">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <xsl:if test="count(CPULoad )&gt;0">
                            <div
                              style="float:left; width:{concat(  CPULoad  , &apos;px&apos; )}; "
                              class="bar">
                              <span>
                                <xsl:text>&#160;</xsl:text>
                              </span>
                            </div>
                            <div style="float:left; ">
                              <span>
                                <xsl:text>&#160;</xsl:text>
                              </span>
                              <xsl:for-each select="CPULoad">
                                <xsl:apply-templates />
                              </xsl:for-each>
                              <span>
                                <xsl:text>%</xsl:text>
                              </span>
                            </div>
                          </xsl:if>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </xsl:for-each>
              </div>
              <div id="hardware">
                <xsl:for-each select="Hardware">
                  <h2>
                    <span>
                      <xsl:text>Hardware Information</xsl:text>
                    </span>
                  </h2>
                  <xsl:for-each select="CPU">
                    <table border="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="width:160px; ">
                            <span>
                              <xsl:text>Processors</xsl:text>
                            </span>
                          </td>
                          <td>
                            <xsl:for-each select="Number">
                              <xsl:apply-templates />
                            </xsl:for-each>
                          </td>
                        </tr>
                        <tr class="odd">
                          <td style="width:160px; ">
                            <span>
                              <xsl:text>Model</xsl:text>
                            </span>
                          </td>
                          <td>
                            <xsl:for-each select="Model">
                              <xsl:apply-templates />
                            </xsl:for-each>
                          </td>
                        </tr>
                        <tr>
                          <td style="width:160px; ">
                            <span>
                              <xsl:text>CPU Speed</xsl:text>
                            </span>
                          </td>
                          <td>
                            <xsl:for-each select="Cpuspeed">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text> Mhz</xsl:text>
                            </span>
                          </td>
                        </tr>
                        <tr class="odd">
                          <td style="width:160px; ">
                            <span>
                              <xsl:text>Bus Speed</xsl:text>
                            </span>
                          </td>
                          <td>
                            <xsl:if test="count(Busspeed  )&gt;0">
                              <xsl:for-each select="Busspeed">
                                <xsl:apply-templates />
                              </xsl:for-each>
                              <span>
                                <xsl:text> Mhz</xsl:text>
                              </span>
                            </xsl:if>
                          </td>
                        </tr>
                        <tr>
                          <td style="width:160px; ">
                            <span>
                              <xsl:text>Cache Size</xsl:text>
                            </span>
                          </td>
                          <td>
                            <xsl:if test="count( Cache )&gt;0">
                              <xsl:for-each select="Cache">
                                <xsl:apply-templates />
                              </xsl:for-each>
                              <span>
                                <xsl:text> KB</xsl:text>
                              </span>
                            </xsl:if>
                          </td>
                        </tr>
                        <tr class="odd">
                          <td style="width:160px; ">
                            <span>
                              <xsl:text>System Bogomips</xsl:text>
                            </span>
                          </td>
                          <td>
                            <xsl:if test="count( Bogomips ) &gt;0">
                              <xsl:for-each select="Bogomips">
                                <xsl:apply-templates />
                              </xsl:for-each>
                            </xsl:if>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </xsl:for-each>
                  <xsl:for-each select="PCI">
                    <h3>
                      <span>
                        <xsl:text>PCI Devices</xsl:text>
                      </span>
                    </h3>
                    <table style="display:block; " cellspacing="0" id="pciTable"
                      width="100%">
                      <tbody>
                        <tr>
                          <td>
                            <ul style="margin-left:10px; ">
                              <xsl:for-each select="Device">
                                <li>
                                  <xsl:for-each select="Name">
                                    <xsl:apply-templates />
                                  </xsl:for-each>
                                </li>
                              </xsl:for-each>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </xsl:for-each>
                  <xsl:for-each select="IDE">
                    <h3 class="odd">
                      <span>
                        <xsl:text>IDE Devices</xsl:text>
                      </span>
                    </h3>
                    <table style="display:block; " cellspacing="0" class="odd"
                      id="ideTable" width="100%">
                      <tbody>
                        <tr>
                          <td>
                            <ul style="margin-left:10px; ">
                              <xsl:for-each select="Device">
                                <li>
                                  <xsl:for-each select="Name">
                                    <xsl:apply-templates />
                                  </xsl:for-each>
                                  <xsl:if test="count( Capacity )&gt;0">
                                    <span>
                                      <xsl:text> (</xsl:text>
                                    </span>
                                    <xsl:for-each select="Capacity">
                                      <xsl:apply-templates />
                                    </xsl:for-each>
                                    <span>
                                      <xsl:text> KB)</xsl:text>
                                    </span>
                                  </xsl:if>
                                </li>
                              </xsl:for-each>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </xsl:for-each>
                  <xsl:for-each select="SCSI">
                    <h3>
                      <span>
                        <xsl:text>SCSI Devices</xsl:text>
                      </span>
                    </h3>
                    <table cellspacing="0" id="scsiTable" width="100%">
                      <tbody>
                        <tr>
                          <td style="display:block; ">
                            <ul style="margin-left:10px; ">
                              <xsl:for-each select="Device">
                                <li>
                                  <xsl:for-each select="Name">
                                    <xsl:apply-templates />
                                  </xsl:for-each>
                                  <xsl:if test="count( Capacity )&gt;0">
                                    <span>
                                      <xsl:text> (</xsl:text>
                                    </span>
                                    <xsl:for-each select="Capacity">
                                      <xsl:apply-templates />
                                    </xsl:for-each>
                                    <span>
                                      <xsl:text> KB)</xsl:text>
                                    </span>
                                  </xsl:if>
                                </li>
                              </xsl:for-each>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </xsl:for-each>
                  <xsl:for-each select="USB">
                    <h3 class="odd">
                      <span>
                        <xsl:text>USB Devices</xsl:text>
                      </span>
                    </h3>
                    <table cellspacing="0" class="odd" id="usbTable"
                      width="100%">
                      <tbody>
                        <tr>
                          <td>
                            <ul style="margin-left:10px; ">
                              <xsl:for-each select="Device">
                                <li>
                                  <xsl:for-each select="Name">
                                    <xsl:apply-templates />
                                  </xsl:for-each>
                                </li>
                              </xsl:for-each>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </xsl:for-each>
                </xsl:for-each>
              </div>
              <div id="memory">
                <xsl:for-each select="Memory">
                  <h2>
                    <span>
                      <xsl:text>Memory Usage</xsl:text>
                    </span>
                  </h2>
                  <table border="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th style="width:200px; ">
                          <span>
                            <xsl:text>Type</xsl:text>
                          </span>
                        </th>
                        <th style="width:285px; ">
                          <span>
                            <xsl:text>Usage</xsl:text>
                          </span>
                        </th>
                        <th style="width:100px; " class="right">
                          <span>
                            <xsl:text>Free</xsl:text>
                          </span>
                        </th>
                        <th style="width:100px; " class="right">
                          <span>
                            <xsl:text>Used</xsl:text>
                          </span>
                        </th>
                        <th style="width:100px; " class="right">
                          <span>
                            <xsl:text>Size</xsl:text>
                          </span>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="odd">
                        <td style="width:200px; ">
                          <span>
                            <xsl:text>Physical Memory</xsl:text>
                          </span>
                        </td>
                        <td style="width:285px; ">
                          <div
                            style="float:left; width:{concat(  Percent  , &apos;px&apos; )}; "
                            class="bar">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                          </div>
                          <div style="float:left; ">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                            <xsl:for-each select="Percent">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text>%</xsl:text>
                            </span>
                          </div>
                        </td>
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Free">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Used">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Total">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td style="width:200px; ">
                          <span>
                            <xsl:text>- Kernel + applications</xsl:text>
                          </span>
                        </td>
                        <td style="width:285px; ">
                          <div
                            style="float:left; width:{concat(  AppPercent  , &apos;px&apos; )}; "
                            class="bar">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                          </div>
                          <div style="float:left; ">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                            <xsl:for-each select="AppPercent">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text>%</xsl:text>
                            </span>
                          </div>
                        </td>
                        <td style="width:100px; " class="right" />
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="App">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right" />
                      </tr>
                      <tr>
                        <td style="width:200px; ">
                          <span>
                            <xsl:text>- Buffers</xsl:text>
                          </span>
                        </td>
                        <td style="width:285px; ">
                          <div
                            style="float:left; width:{concat(  BuffersPercent  , &apos;px&apos; )}; "
                            class="bar">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                          </div>
                          <div>
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                            <xsl:for-each select="BuffersPercent">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text>%</xsl:text>
                            </span>
                          </div>
                        </td>
                        <td style="width:100px; " class="right" />
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Buffers">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right" />
                      </tr>
                      <tr>
                        <td style="width:200px; ">
                          <span>
                            <xsl:text>- Cached</xsl:text>
                          </span>
                        </td>
                        <td style="width:285px; ">
                          <div
                            style="float:left; width:{concat(  CachedPercent  , &apos;px&apos; )}; "
                            class="bar">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                          </div>
                          <div style="float:left; ">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                            <xsl:for-each select="CachedPercent">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text>%</xsl:text>
                            </span>
                          </div>
                        </td>
                        <td style="width:100px; " class="right" />
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Cached">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right" />
                      </tr>
                    </tbody>
                  </table>
                </xsl:for-each>
                <xsl:for-each select="Swap">
                  <table border="0" cellspacing="0" width="100%">
                    <tbody>
                      <tr class="odd">
                        <td style="width:200px; ">
                          <span>
                            <xsl:text>Disk Swap</xsl:text>
                          </span>
                        </td>
                        <td style="width:285px; ">
                          <div style="float:left; " class="bar">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                          </div>
                          <div style="float:left; ">
                            <span>
                              <xsl:text>&#160;</xsl:text>
                            </span>
                            <xsl:for-each select="Percent">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text>%</xsl:text>
                            </span>
                          </div>
                        </td>
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Free">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Used">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                        <td style="width:100px; " class="right">
                          <xsl:for-each select="Total">
                            <xsl:apply-templates />
                          </xsl:for-each>
                          <span>
                            <xsl:text> KB</xsl:text>
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </xsl:for-each>
                <xsl:for-each select="Swapdevices">
                  <table border="0" cellspacing="0">
                    <tbody>
                      <xsl:for-each select="Mount">
                        <tr>
                          <td style="width:200px; ">
                            <span>
                              <xsl:text>- </xsl:text>
                            </span>
                            <xsl:for-each select="Device">
                              <xsl:apply-templates />
                            </xsl:for-each>
                          </td>
                          <td style="width:285px; ">
                            <div style="float:left; " class="bar">
                              <span>
                                <xsl:text>&#160;</xsl:text>
                              </span>
                            </div>
                            <div style="float:left; ">
                              <span>
                                <xsl:text>&#160;</xsl:text>
                              </span>
                              <xsl:for-each select="Percent">
                                <xsl:apply-templates />
                              </xsl:for-each>
                              <span>
                                <xsl:text>%</xsl:text>
                              </span>
                            </div>
                          </td>
                          <td style="width:100px; " class="right" colspan="2">
                            <xsl:for-each select="Free">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text> KB</xsl:text>
                            </span>
                          </td>
                          <td style="width:100px; " class="right">
                            <xsl:for-each select="Used">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text> KB</xsl:text>
                            </span>
                          </td>
                          <td style="width:100px; " class="right">
                            <xsl:for-each select="Size">
                              <xsl:apply-templates />
                            </xsl:for-each>
                            <span>
                              <xsl:text> KB</xsl:text>
                            </span>
                          </td>
                        </tr>
                      </xsl:for-each>
                    </tbody>
                  </table>
                </xsl:for-each>
              </div>
              <div id="filesystem">
                <h2>
                  <span>
                    <xsl:text>Mounted Filesystems</xsl:text>
                  </span>
                </h2>
                <table cellspacing="0" class="stripMe" id="filesystemTable">
                  <thead>
                    <tr>
                      <th style="width:100px; ">
                        <span>
                          <xsl:text>Mountpoint</xsl:text>
                        </span>
                      </th>
                      <th style="width:50px; ">
                        <span>
                          <xsl:text>Type</xsl:text>
                        </span>
                      </th>
                      <th style="width:120px; ">
                        <span>
                          <xsl:text>Partition</xsl:text>
                        </span>
                      </th>
                      <th>
                        <span>
                          <xsl:text>Usage</xsl:text>
                        </span>
                      </th>
                      <th style="width:100px; " class="right">
                        <span>
                          <xsl:text>Free</xsl:text>
                        </span>
                      </th>
                      <th style="width:100px; " class="right">
                        <span>
                          <xsl:text>Used</xsl:text>
                        </span>
                      </th>
                      <th style="width:100px; " class="right">
                        <span>
                          <xsl:text>Size</xsl:text>
                        </span>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <xsl:for-each select="FileSystem">
                      <xsl:for-each select="Mount">
                        <tr>
                          <td style="width:100px; ">
                            <xsl:for-each select="MountPoint">
                              <xsl:apply-templates />
                            </xsl:for-each>
                          </td>
                          <td style="width:50px; ">
                            <xsl:for-each select="Type">
                              <xsl:apply-templates />
                            </xsl:for-each>
                          </td>
                          <td style="width:120px; ">
                            <xsl:for-each select="Device">
                              <xsl:apply-templates />
                            </xsl:for-each>
                          </td>
                          <td style="width:285px; ">
                            <div
                              style="float:left; width:{concat(  Percent  , &apos;px&apos; )}; "
                              class="bar">
                              <span>
                                <xsl:text>&#160;</xsl:text>
                              </span>
                            </div>
                            <div style="float:left; ">
                              <span>
                                <xsl:text>&#160;</xsl:text>
                              </span>
                              <xsl:for-each select="Percent">
                                <xsl:apply-templates />
                              </xsl:for-each>
                              <span>
                                <xsl:text>%</xsl:text>
                              </span>
                              <xsl:if test="count(Inodes )&gt;0">
                                <span>
                                  <xsl:text> (</xsl:text>
                                </span>
                                <xsl:for-each select="Inodes">
                                  <span style="font-style:italic; ">
                                    <xsl:apply-templates />
                                  </span>
                                </xsl:for-each>
                                <span>
                                  <xsl:text>%)</xsl:text>
																</span>
															</xsl:if>
														</div>
													</td>
													<td style="width:100px; " class="right">
														<xsl:for-each select="Free">
															<xsl:apply-templates/>
														</xsl:for-each>
														<span>
															<xsl:text> KB</xsl:text>
														</span>
													</td>
													<td style="width:100px; " class="right">
														<xsl:for-each select="Used">
															<xsl:apply-templates/>
														</xsl:for-each>
														<span>
															<xsl:text> KB</xsl:text>
														</span>
													</td>
													<td style="width:100px; " class="right">
														<xsl:for-each select="Size">
															<xsl:apply-templates/>
														</xsl:for-each>
														<span>
															<xsl:text> KB</xsl:text>
														</span>
													</td>
												</tr>
											</xsl:for-each>
										</xsl:for-each>
									</tbody>
								</table>
							</div>
							<div id="network">
								<h2>
									<span>
										<xsl:text>Network Usage</xsl:text>
									</span>
								</h2>
								<table cellspacing="0" class="stripMe" id="networkTable">
									<thead>
										<tr>
											<th>
												<span>
													<xsl:text>Device</xsl:text>
												</span>
											</th>
											<th class="right" width="60px">
												<span>
													<xsl:text>Received</xsl:text>
												</span>
											</th>
											<th class="right" width="60px">
												<span>
													<xsl:text>Send</xsl:text>
												</span>
											</th>
											<th class="right" width="60px">
												<span>
													<xsl:text>Err/Drop</xsl:text>
												</span>
											</th>
										</tr>
									</thead>
									<tbody>
										<xsl:for-each select="Network">
											<xsl:for-each select="NetDevice">
												<tr>
													<td>
														<xsl:for-each select="Name">
															<xsl:apply-templates/>
														</xsl:for-each>
													</td>
													<td class="right" width="60px">
														<span>
															<xsl:value-of select="round(RxBytes div 1024)"/>
														</span>
														<span>
															<xsl:text> KB</xsl:text>
														</span>
													</td>
													<td class="right" width="60px">
														<span>
															<xsl:text>&#160;</xsl:text>
														</span>
														<span>
															<xsl:value-of select="round(TxBytes div 1024)"/>
														</span>
														<span>
															<xsl:text> KB</xsl:text>
														</span>
													</td>
													<td class="right" width="60px">
														<xsl:for-each select="Err">
															<xsl:apply-templates/>
														</xsl:for-each>
														<span>
															<xsl:text>/</xsl:text>
														</span>
														<xsl:for-each select="Drops">
															<xsl:apply-templates/>
														</xsl:for-each>
													</td>
												</tr>
											</xsl:for-each>
										</xsl:for-each>
									</tbody>
								</table>
							</div>
							<xsl:if test="count(  MBinfo/Voltage  ) &gt; 0">
								<div id="voltage">
									<h2>
										<span>
											<xsl:text>Voltage</xsl:text>
										</span>
									</h2>
									<table cellspacing="0" class="stripMe" id="voltageTable">
										<thead>
											<tr>
												<th>
													<span>
														<xsl:text>Label</xsl:text>
													</span>
												</th>
												<th class="right">
													<span>
														<xsl:text>Value</xsl:text>
													</span>
												</th>
												<th class="right" width="60px">
													<span>
														<xsl:text>Min</xsl:text>
													</span>
												</th>
												<th class="right" width="60px">
													<span>
														<xsl:text>Max</xsl:text>
													</span>
												</th>
											</tr>
										</thead>
										<tbody>
											<xsl:for-each select="MBinfo">
												<xsl:for-each select="Voltage">
													<xsl:for-each select="Item">
														<tr>
															<td>
																<xsl:for-each select="Label">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
															<td class="right">
																<xsl:for-each select="Value">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<span>
																	<xsl:text> V</xsl:text>
																</span>
															</td>
															<td class="right" width="60px">
																<xsl:for-each select="Min">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<span>
																	<xsl:text> V</xsl:text>
																</span>
															</td>
															<td class="right" width="60px">
																<xsl:for-each select="Max">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<span>
																	<xsl:text> V</xsl:text>
																</span>
															</td>
														</tr>
													</xsl:for-each>
												</xsl:for-each>
											</xsl:for-each>
										</tbody>
									</table>
								</div>
							</xsl:if>
							<xsl:if test="(count(  MBinfo/Temperature ) &gt; 0) or (count( HDDTemp/Item ) &gt; 0)">
								<div id="temp">
									<h2>
										<span>
											<xsl:text>Temperature</xsl:text>
										</span>
									</h2>
									<table cellspacing="0" class="stripMe" id="tempTable">
										<thead>
											<tr>
												<th>
													<span>
														<xsl:text>Label</xsl:text>
													</span>
												</th>
												<th class="right" width="60px">
													<span>
														<xsl:text>Value</xsl:text>
													</span>
												</th>
												<th class="right" width="60px">
													<span>
														<xsl:text>Limit</xsl:text>
													</span>
												</th>
											</tr>
										</thead>
										<tbody>
											<xsl:for-each select="MBinfo">
												<xsl:for-each select="Temperature">
													<xsl:for-each select="Item">
														<tr>
															<td>
																<xsl:for-each select="Label">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
															<td class="right" width="60px">
																<xsl:for-each select="Value">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<span>
																	<xsl:text> C</xsl:text>
																</span>
															</td>
															<td class="right" width="60px">
																<xsl:for-each select="Limit">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<span>
																	<xsl:text> C</xsl:text>
																</span>
															</td>
														</tr>
													</xsl:for-each>
												</xsl:for-each>
											</xsl:for-each>
										</tbody>
									</table>
									<table cellspacing="0" class="stripMe" id="tempTable">
										<tbody>
											<xsl:for-each select="HDDTemp">
												<xsl:for-each select="Item">
													<tr>
														<td>
															<xsl:for-each select="Model">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
														<td class="right" width="60px">
															<span>
																<xsl:value-of select="concat( Value, &apos; C&apos;)"/>
															</span>
														</td>
														<td class="right" width="60px">
															<xsl:for-each select="Limit">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</tr>
												</xsl:for-each>
											</xsl:for-each>
										</tbody>
									</table>
								</div>
							</xsl:if>
							<xsl:if test="count(   MBinfo/Fans ) &gt; 0">
								<div id="fan">
									<h2>
										<span>
											<xsl:text>Fans</xsl:text>
										</span>
									</h2>
									<table cellspacing="0" class="stripMe" id="fanTable">
										<thead>
											<tr>
												<th>
													<span>
														<xsl:text>Label</xsl:text>
													</span>
												</th>
												<th class="right" width="60px">
													<span>
														<xsl:text>Value</xsl:text>
													</span>
												</th>
												<th class="right" width="60px">
													<span>
														<xsl:text>Min</xsl:text>
													</span>
												</th>
											</tr>
										</thead>
										<tbody>
											<xsl:for-each select="MBinfo">
												<xsl:for-each select="Fans">
													<xsl:for-each select="Item">
														<tr>
															<td>
																<xsl:for-each select="Label">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
															<td class="right" width="60px">
																<xsl:for-each select="Value">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
															<td class="right" width="60px">
																<xsl:for-each select="Min">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
														</tr>
													</xsl:for-each>
												</xsl:for-each>
											</xsl:for-each>
										</tbody>
									</table>
								</div>
							</xsl:if>
							<xsl:if test="count(UPSinfo) &gt; 0">
								<div id="ups">
									<h2>
										<span>
											<xsl:text>UPS information</xsl:text>
										</span>
									</h2>
									<xsl:for-each select="UPSinfo">
										<xsl:for-each select="Ups">
											<table cellspacing="0" class="stripMe" id="upsTable">
												<thead>
													<tr>
														<th colspan="2" style="text-align: center">
															<strong>
																<xsl:for-each select="Name">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<xsl:text> (</xsl:text>
																<xsl:for-each select="Mode">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<xsl:text>)</xsl:text>
															</strong>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td style="width:160px">
															<xsl:text>Model</xsl:text>
														</td>
														<td style="width:250px">
															<xsl:for-each select="Model">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</tr>
													<tr>
														<td style="width:160px">
															<xsl:text>Started</xsl:text>
														</td>
														<td style="width:250px">
															<xsl:for-each select="StartTime">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</tr>
													<tr>
														<td style="width:160px">
															<xsl:text>Status</xsl:text>
														</td>
														<td style="width:250px">
															<xsl:for-each select="Status">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</tr>
													<xsl:if test="string(UPSTemperature)">
														<tr>
															<td style="width:160px">
																<xsl:text>Temperature</xsl:text>
															</td>
															<td style="width:250px">
																<xsl:for-each select="UPSTemperature">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
														</tr>
													</xsl:if>
													<xsl:if test="string(OutagesCount)">
														<tr>
															<td style="width:160px">
																<xsl:text>Outages</xsl:text>
															</td>
															<td style="width:250px">
																<xsl:for-each select="OutagesCount">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
														</tr>
													</xsl:if>
													<xsl:if test="string(LastOutage)">
														<tr>
															<td style="width:160px">
																<xsl:text>Last outage cause</xsl:text>
															</td>
															<td style="width:250px">
																<xsl:for-each select="LastOutage">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
														</tr>
													</xsl:if>
													<xsl:if test="string(LastOutageFinish)">
														<tr>
															<td style="width:160px">
																<xsl:text>Last outage timestamp</xsl:text>
															</td>
															<td style="width:250px">
																<xsl:for-each select="LastOutageFinish">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
														</tr>
													</xsl:if>
													<xsl:if test="string(LineVoltage)">
														<tr>
															<td style="width:160px">
																<xsl:text>Line voltage</xsl:text>
															</td>
															<td style="width:250px">
																<xsl:for-each select="LineVoltage">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</td>
														</tr>
													</xsl:if>
													<xsl:if test="string(LoadPercent)">
														<tr>
															<td style="width:160px">
																<xsl:text>Load percent</xsl:text>
															</td>
															<td style="width:250px">
																<div style="float:left; width:{concat(LoadPercent, &apos;px&apos; )}; " class="bar">
                              										<span>
                                										<xsl:text>&#160;</xsl:text>
                              										</span>
                            									</div>
                            									<div style="float:left; ">
                              										<span>
                                										<xsl:text>&#160;</xsl:text>
                              										</span>
                              										<xsl:for-each select="LoadPercent">
                                										<xsl:apply-templates />
                              										</xsl:for-each>
                              										<span>
                                										<xsl:text>%</xsl:text>
                              										</span>
																</div>
															</td>
														</tr>
													</xsl:if>
													<xsl:if test="string(BatteryVoltage)">
														<tr>
															<td style="width:160px">
																<xsl:text>Battery voltage</xsl:text>
															</td>
															<td style="width:250px">
																<xsl:for-each select="BatteryVoltage">
																	<xsl:apply-templates/>
																</xsl:for-each>
																<xsl:text> V</xsl:text>
															</td>
														</tr>
													</xsl:if>
													<tr>
														<td style="width:160px">
															<xsl:text>Battery charge</xsl:text>
														</td>
														<td style="width:250px">
															<div style="float:left; width:{concat(BatteryChargePercent, &apos;px&apos; )}; " class="bar">
                          										<span>
                               										<xsl:text>&#160;</xsl:text>
                           										</span>
                           									</div>
                          									<div style="float:left; ">
                           										<span>
                               										<xsl:text>&#160;</xsl:text>
                           										</span>
                           										<xsl:for-each select="BatteryChargePercent">
                             										<xsl:apply-templates />
                           										</xsl:for-each>
                           										<span>
                             										<xsl:text>%</xsl:text>
                           										</span>
															</div>
														</td>
													</tr>
													<tr>
														<td style="width:160px">
															<xsl:text>Time left on batteries</xsl:text>
														</td>
														<td style="width:250px">
															<xsl:for-each select="TimeLeftMinutes">
																<xsl:apply-templates/>
															</xsl:for-each>
															<xsl:text> minutes</xsl:text>
														</td>
													</tr>
												</tbody>
											</table>
										</xsl:for-each>
									</xsl:for-each>
								</div>
							</xsl:if>
							<div id="footer">
								<span>
									<xsl:text>Created by </xsl:text>
								</span>
								<a>
									<xsl:choose>
										<xsl:when test="substring(string(&apos;http://phpsysinfo.sourceforge.net/&apos;), 1, 1) = '#'">
											<xsl:attribute name="href">
												<xsl:value-of select="&apos;http://phpsysinfo.sourceforge.net/&apos;"/>
											</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="href">
												<xsl:if test="substring(string(&apos;http://phpsysinfo.sourceforge.net/&apos;), 2, 1) = ':'">
													<xsl:text>file:///</xsl:text>
												</xsl:if>
												<xsl:value-of select="translate(string(&apos;http://phpsysinfo.sourceforge.net/&apos;), '&#x5c;', '/')"/>
											</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
									<span>
										<xsl:text>phpSysInfo - </xsl:text>
									</span>
									<xsl:for-each select="Generation">
										<xsl:for-each select="@version">
											<span>
												<xsl:value-of select="string(.)"/>
											</span>
										</xsl:for-each>
									</xsl:for-each>
								</a>
							</div>
						</div>
					</xsl:for-each>
				</xsl:for-each>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
