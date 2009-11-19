import java.util.HashMap;

import org.xml.sax.Attributes;
import org.xml.sax.helpers.DefaultHandler;

class JasperConfigParser extends DefaultHandler {

	private HashMap<String, CustomJasperReport> reportsHash = null;
	private HashMap<String, JasperConnection> connectionsHash = null;
	private HashMap<String, String> parametersHash = null;

	public JasperConfigParser() {

		this.connectionsHash = new HashMap<String, JasperConnection>();
		this.reportsHash = new HashMap<String, CustomJasperReport>();
		this.parametersHash = new HashMap<String, String>();

	}

	public void startElement(String uri, String localName, String qName,
			Attributes attributes) {

		if (localName.equals("Connection")) {

			// use the connection name as a key value
			this.connectionsHash.put(attributes.getValue("name"),
					new JasperConnection(attributes.getValue("name"),
							attributes.getValue("host"), attributes
									.getValue("db"), attributes
									.getValue("port"), attributes
									.getValue("dbname"), attributes
									.getValue("username"), attributes
									.getValue("password")

					));

		} else if (localName.equals("Report")) {
			// the Connections section
			// should *always* come
			// before the Reports
			// section.

			this.reportsHash.put(attributes.getValue("name"),
					new CustomJasperReport(attributes.getValue("name"),
							attributes.getValue("source"), connectionsHash
									.get(attributes.getValue("connection"))

					));

		} else if (localName.equals("StaticData")) {

			this.parametersHash.put(attributes.getValue("key"), attributes
					.getValue("value"));

		}

	}

	public HashMap<String, CustomJasperReport> getReportsHash() {
		return this.reportsHash;
	}

	public HashMap<String, String> getParametersHash() {
		return this.parametersHash;
	}

}
