
import java.util.HashMap;

import org.xml.sax.Attributes;
import org.xml.sax.helpers.DefaultHandler;

class JasperConfigParser extends DefaultHandler {

	private HashMap<String, CustomJasperReport> reportsHash = null;
	private HashMap<String, String> parametersHash = null;

	public JasperConfigParser() {

		this.reportsHash = new HashMap<String, CustomJasperReport>();
		this.parametersHash = new HashMap<String, String>();
	//	System.err.println("JasperConfigParser");

	}

  
	@Override
	public void startElement(
			String uri,
			String localName,
			String qName,
			Attributes attributes) {
   
//		System.out.printf("Start Element : %s%n", qName);

		if (qName.equals("Report")) {
			// the Connections section
			// should *always* come
			// before the Reports
			// section.
			this.reportsHash.put(attributes.getValue("name"),
					new CustomJasperReport(attributes.getValue("name"),
							attributes.getValue("source")));

		} else if (qName.equals("StaticData")) {

			this.parametersHash.put(attributes.getValue("key"), attributes.getValue("value"));
		}

	}

	public HashMap<String, CustomJasperReport> getReportsHash() {
		return this.reportsHash;
	}

	public HashMap<String, String> getParametersHash() {
		return this.parametersHash;
	}

}
