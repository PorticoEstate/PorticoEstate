import java.sql.DriverManager;

//import java.sql.SQLException;
//import com.mysql.jdbc.Connection;

class JasperConnection {

	private String connection_string;
	private String username;
	private String password;
	private int db_type;
	
	private final static int DUMMYCONNECTION = 0;
	private final static int MYSQLCONNECTION = 1;
	private final static int POSTGRESQLCONNECTION = 2;
	
	// the real connection object
	private java.sql.Connection connection;

	public JasperConnection(String connection_string, String username, String password) {

		this.connection_string = connection_string;
		this.username = username;
		this.password = password;
		
		// use this ugly hack to determine DB type
		if (this.connection_string.startsWith("jdbc:mysql:")) {
			this.db_type = JasperConnection.MYSQLCONNECTION;
		} else if (this.connection_string.startsWith("jdbc:postgresql:")) {
			this.db_type = JasperConnection.POSTGRESQLCONNECTION;
		} else {
			this.db_type = JasperConnection.DUMMYCONNECTION;
		}
		
	}

	
	public java.sql.Connection makeConnection() {

		if (this.db_type == MYSQLCONNECTION) {

			try {
				Class.forName("com.mysql.jdbc.Driver").newInstance();
			} catch (Exception ex) {

//				System.err.println("Unable to load MySQL driver: "
//						+ ex.getMessage());
				System.exit(209);

			}


			try {
				connection = DriverManager.getConnection(this.connection_string,
						this.username, this.password);
			} catch (Exception ex) {

//				System.err.printf("Unable to connect to MySQL database (%s): %s",
//						connection_url, ex.getMessage());
				System.exit(211);

			}

		} else if (this.db_type == POSTGRESQLCONNECTION) { // postgresql

			try {
				Class.forName("org.postgresql.Driver").newInstance();
			} catch (Exception ex) {

//				System.err.println("Unable to load PostgreSQL driver: "
//						+ ex.getMessage());
				System.exit(210);

			}

			try {
				connection = DriverManager.getConnection(this.connection_string,
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
