#!/usr/bin/perl
  #**************************************************************************\
  # phpGroupWare XML-RPC perl gateway                                        *
  # http://www.phpgroupware.org                                              *
  # Written by Miles Lott <milosch@phpgroupware.org>                         *
  # --------------------------------------------                             *
  #  This program is free software; you can redistribute it and/or modify it *
  #  under the terms of the GNU General Public License as published by the   *
  #  Free Software Foundation; either version 2 of the License, or (at your  *
  #  option) any later version.                                              *
  #**************************************************************************/

  # $Id: gateway.pl 10759 2002-08-31 02:31:16Z milosch $ #

	# This daemon listens on the selected port.  It then allows for simple
	#  login to a phpGroupWare server in user or server mode.  With some
	#  work, any function call should be possible here, given acl rights.
	#for ($i=30;$i<64;$i++)
	#{
	#	print $i . ': "' . chr($i) . '"' . "\n";
	#}
	#exit;
	# Only a few user serviceable parts here.  These can also be set
	#  within the shell.
	$host      = 'www.phpgroupware.org';
	$uri       = '/demo/xmlrpc.php';
	$listen_ip = '127.0.0.1';
	$debug     = 0;

	$form = '<form method="GET">
<input name="request">
</form>
';

	# Note the following requirements:
	use IO::Socket;
	use POSIX 'WNOHANG';
	use Frontier::Client;	# The modified one (see phpgwapi/doc/xmlrpc)
	use Data::Dumper;

	use constant PORT => 12000;

	$SIG{CHLD} = sub
	{
		while(waitpid(-1,WNOHANG)>0 ) {}
	};

	my $listen = IO::Socket::INET->new(
		LocalPort => PORT,
		LocalAddr => "$listen_ip",
		Listen    => 20,
		Proto     => 'tcp',
		Reuse     => 1
	);
	die "Can't create listening socket: $@" unless $listen;
	warn "Server ready.  Waiting for connections...\n";

	while ( my $connection = $listen->accept )
	{
		die "Can't fork: $!" unless defined ( my $child = fork() );
		if ( $child == 0 )
		{
			$listen->close;
			interact( $connection );
			exit 0;
		}
	}
	continue
	{
		$connection->close;
	}

	sub interact
	{
		my $sock = shift;
		my $hersockaddr    = $sock->peername;
		my ($port, $iaddr) = sockaddr_in($hersockaddr);
		my $herhostname    = gethostbyaddr($iaddr, AF_INET);
		#$herstraddr    = inet_ntoa($iaddr);

		print "Connection from: " . $herhostname . ' on ' . $port . "\n";

		# fdopen is from IO::Handle, which is parent to IO::Socket
		STDIN->fdopen( $sock,"r" ) || die "Can't reopen STDIN: $!";
		STDOUT->fdopen( $sock,"w" ) || die "Can't reopen STDOUT: $!";
		STDERR->fdopen( $sock,"w" ) || die "Can't reopen STDERR: $!";
		STDOUT->autoflush(1);

		$_ = <STDIN>;
		if(!/GET/)
		{
			print STDOUT "\n" . 'Welcome!  Type help to see a list of commands.' . "\n";
			print STDOUT '> ';
		}
		else
		{
			print STDOUT '<html><head></head>';
			print STDOUT "\n" . 'Welcome!  Type help to see a list of commands.' . "\n";
			print STDOUT '> ';
			print STDOUT $form;
			print STDOUT '</html>';
			#exit 0;
		}

		while(<STDIN>)
		{
			if(/request=/)
			{
				my @tmp = split('=',$_);
				$_ = @tmp[1];
				chomp $_;
				$_ =~ s/chr(43)/\s/g;
				#my $tm = split("chr(43)",$_);
				#if($tm[1])
				#{
				#	$_ = join(' ',@tm);
				#}
				print 'Modified request="' . $_ . '"';
				#$_ =~ s/GET \///g;
				#$_ =~ s/\?//g;
				#$_ =~ s/request=//g;
			}
			if(/^slogin/)
			{
				($null,$username,$password,$domain) = split(' ', $_);

				print STDOUT "\n" . 'Logging in as peer server... ';
				my $result = &slogin($username,$password,$domain);
				$sessionid = ${$result}{'sessionid'};
				$kp3       = ${$result}{'kp3'};
				$domain    = ${$result}{'domain'};

				if($sessionid)
				{
					print STDOUT 'success!' . "\n";
					#print STDOUT 'Got sessionid="' . $sessionid . '", ';
					#print STDOUT 'kp3="' . $kp3 . '", ';
					#print STDOUT 'domain="' . $domain . '".' . "\n";
				}
				else
				{
					print STDOUT 'failed :(' . "\n\n";
				}
			}
			elsif(/^login/)
			{
				($null,$username,$password,$domain) = split(' ', $_);

				print STDOUT "\n" . 'Logging in... ';
				my $result = &login($username,$password,$domain);
				$sessionid = ${$result}{'sessionid'};
				$kp3       = ${$result}{'kp3'};
				$domain    = ${$result}{'domain'};

				if($sessionid)
				{
					print STDOUT 'success!' . "\n";
					#print STDOUT 'Got sessionid="' . $sessionid . '", ';
					#print STDOUT 'kp3="' . $kp3 . '", ';
					#print STDOUT 'domain="' . $domain . '".' . "\n";
				}
				else
				{
					print STDOUT 'failed :(' . "\n\n";
				}
			}
			elsif(/^logout/)
			{
				if($sessionid)
				{
					print STDOUT "\n" . 'Logging out... ';
					my $result = &logout($sessionid,$kp3,$domain);

					if($result)
					{
						print STDOUT 'done!' . "\n\n";
					}
					else
					{
						print STDOUT 'failed :(' . "\n\n";
					}
					$sessionid = $kp3 = $domain = undef;
				}
			}
			elsif(/^env/)
			{
				print STDOUT 'host     : ' . $host . "\n";
				print STDOUT 'uri      : ' . $uri . "\n";
				print STDOUT 'debug    : ' . $debug;
				if($debug)
				{
					print STDOUT ' (True)' . "\n";
				}
				else
				{
					print STDOUT ' (False)' . "\n";
				}
				print STDOUT 'sessionid: ' . $sessionid . "\n";
				print STDOUT 'kp3      : ' . $kp3 . "\n";
			}
			elsif(/^set/)
			{
				($null,$var,$value) = split(' ',$_);
				if($var and $value)
				{
					eval("\$$var = \"$value\"");
					print STDOUT $var . ' set to: ' . $value . "\n";
					$var = $value = undef;
				}
			}
			elsif(/^help/ or /^\?/)
			{
				$methname  = undef;
				($null,$methname) = split(' ', $_);
				if($sessionid)
				{
					my $result;
					if($methname)
					{
						$result = &method_help($methname);
					}
					else
					{
						print "\nRemote commands: ";
						$result = &list_methods;
					}
					print Dumper($result);
				}
				print STDOUT "\nLocal commands: " . 'login, slogin, logout, env, set, help, exit' . "\n\n";
			}
			elsif(/exit/ or /quit/)
			{
				print STDOUT 'Bye!' . "\n\n";
				exit 0;
			}
			elsif(/HTTP/ or /Referer/ or /User-Agent/ or /Host/ or /Connection/ or /Accept/)
			{
				exit 0;
			}
			else
			{
				my @args = split(/ /);
				$result = &call_user_functions(@args);
				print Dumper($result);
			}
			print STDOUT '> ';
		}
	}

	sub connect
	{
		my $encoding  = undef;
		my $proxy     = undef;

		$server = Frontier::Client->new(
			'url'      => 'http://' . $host . $uri,
			'debug'    => $debug,
			'encoding' => $encoding,
			'proxy'    => $proxy,
			'username' => $sessionid,
			'password' => $kp3
		);
	}

	sub login
	{
		my $username = shift;
		my $password = shift;
		my $domain   = shift;

		&connect;

		eval "\$arglist = \"{'username' => '$username', 'password' => '$password', 'domain' => '$domain' }\"";
		eval "\@arglist = ($arglist)";

		my $result = $server->call('system.login', @arglist);

		return $result;
	}

	sub slogin
	{
		my $username = shift;
		my $password = shift;
		my $domain   = shift;

		&connect;

		eval "\$arglist = \"{'username' => '$username', 'password' => '$password', 'server_name' => '$domain' }\"";
		eval "\@arglist = ($arglist)";

		my $result = $server->call('system.login', @arglist);

		return $result;
	}

	sub logout
	{
		my $sessionid = shift;
		my $kp3       = shift;
		my $domain    = shift;

		&connect;

		eval "\$arglist = \"{'sessionid' => '$sessionid', 'kp3' => '$kp3' }\"";
		eval "\@arglist = ($arglist)";

		my $result = $server->call('system.logout', @arglist);

		return $result;
	}

	sub list_methods
	{
		&connect;
		my $result = $server->call('system.listMethods');
		return $result;
	}

	sub method_help
	{
		my $methname = shift;

		&connect;
		my $result = $server->call('system.methodHelp',"$methname");
		return $result;
	}

	sub call_user_functions
	{
		my $gotargs = 0;
		my $array   = 0;

		print STDOUT 'ahem: ' . scalar(@_);
		if(scalar(@_) > 2)
		{
			$array = 1;
		}

		my $function = shift;
		$function =~ s/\n//g;
		$function =~ s/\r//g;
		if($debug)
		{
			print STDOUT 'Function: ' . $function;
		}

		if($array)
		{
			while(shift)
			{
				$_ =~ s/\n//g;
				$_ =~ s/\r//g;
				$args .= $i . ' => ' . $_;
				if($debug)
				{
					print STDOUT 'GOT: ' . $_ . "\n";
				}
				$gotargs = 1;
			}
			eval "\$arglist = \"{ $args }\"";
			eval "\@arglist = ($arglist)";
		}
		else
		{
			$arglist = shift;
			$arglist =~ s/\n//g;
			$arglist =~ s/\r//g;
			$gotargs = 1;
		}

		&connect;
		#print Dumper($server);

		my $result = '';
		if($gotargs)
		{
			if($array)
			{
				$result = $server->call($function,@arglist);
			}
			else
			{
				$result = $server->call($function,$arglist);
			}
		}
		else
		{
			$result = $server->call($function);
		}
		return $result;
	}

# FIN
1;
