import java.sql.DriverManager;

//import java.sql.SQLException;
//import com.mysql.jdbc.Connection;

class JasperConnection {

	private String name;
	private String host;
	private String db;
	private String port;
	private String dbname;
	private String username;
	private String password;

	// the real connection object
	private java.sql.Connection connection;

	public JasperConnection(String name, String host, String db, String port,
			String dbname, String username, String password) {

		this.name = name;
		this.host = host;
		this.db = db;
		this.port = port;
		this.dbname = dbname;
		this.username = username;
		this.password = password;

	}

	public String getName() {
		return this.name;
	}

	/*
	 * public String getHost() { return this.host; }
	 * 
	 * public String getDb() { return this.db; }
	 * 
	 * public String getPort() { return this.port; }
	 * 
	 * public String getDBName() { return this.dbname; }
	 * 
	 * public String getUsername() { return this.username; }
	 * 
	 * public String getPassword() { return this.password; }
	 */

	public java.sql.Connection makeConnection() {

		String connection_url = null;

		if (this.db.equalsIgnoreCase("mysql")) {

			try {
				Class.forName("com.mysql.jdbc.Driver").newInstance();
			} catch (Exception ex) {

//				System.err.println("Unable to load MySQL driver: "
//						+ ex.getMessage());
				System.exit(209);

			}

			connection_url = "jdbc:mysql://" + this.host + ":" + this.port
					+ "/" + this.dbname;

			try {
				connection = DriverManager.getConnection(connection_url,
						this.username, this.password);
			} catch (Exception ex) {

//				System.err.printf("Unable to connect to MySQL database (%s): %s",
//						connection_url, ex.getMessage());
				System.exit(211);

			}

		} else if (this.db.equalsIgnoreCase("postgresql")) { // postgresql

			try {
				Class.forName("org.postgresql.Driver").newInstance();
			} catch (Exception ex) {

//				System.err.println("Unable to load PostgreSQL driver: "
//						+ ex.getMessage());
				System.exit(210);

			}

			connection_url = "jdbc:postgresql://" + this.host + ":" + this.port
					+ "/" + this.dbname;

			try {
				connection = DriverManager.getConnection(connection_url,
						this.username, this.password);
			} catch (Exception ex) {

//				System.err.println("Unable to connect to PostgreSQL database(" + this.dbname + "): "
//						+ ex.getMessage());
				System.exit(211);

			}

		} else { // dummy ( no connection )
			// some reports may be created with this kind of connection
			return null;

		}

		return connection;

	}



}
