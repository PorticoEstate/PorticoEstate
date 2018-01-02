import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.HashMap;

import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

class JasperEngine {

	public static void main(String[] args) throws IOException {

		File file = new File(args[args.length - 1]);
		InputSource source = null;
		
		try {
			source = new InputSource(new FileInputStream(file));
		} catch (Exception ex) {
			//System.err.println("Missing configuration file");
			System.exit(214);
		}
		
		HashMap<String, CustomJasperReport> reports;
		HashMap<String, String> parameters = null;

		JasperConfigParser jcp = new JasperConfigParser();

		JasperMacros jm = new JasperMacros();

		int output_type = 0; // 0 - PDF (default), 1 = CSV, 2 - XLS
		String report_name = null;
		String connection_string = null;
		String db_username = null;
		String db_password = null;

		for (int i = 0; i < args.length; ++i) {

			if (args[i].equals("-h")) {
				printHelp();
				System.exit(0);
			} else if (args[i].equals("-n")) {
				report_name = args[i + 1];
			} else if (args[i].equals("-p")) {
				jm.loadMacros(args[i + 1]);
			} else if (args[i].equals("-t")) {
				if (args[i + 1].equals("CSV")) {
					output_type = 1;
				} else if (args[i + 1].equals("XLS")) {
					output_type = 2;
				} else if (args[i + 1].equals("XHTML")) {
					output_type = 3;
				} else if (args[i + 1].equals("DOCX")) {
					output_type = 4;
				} else if (!args[i + 1].equals("PDF")) {
					// System.err.printf("Unknown type: %s\n", args[i + 1]);
					// printHelp();
					System.exit(208);
				}
			} else if (args[i].equals("-d")) {
				connection_string = args[i + 1];
			} else if (args[i].equals("-u")) {
				db_username = args[i + 1];
			} else if (args[i].equals("-P")) {
				db_password = args[i + 1];
			}
		}

		if (report_name == null) {
			//System.err.println("Missing report-name");
			System.exit(212);
		}

		if (connection_string == null) {
			//System.err.println("Missing connection string");
			System.exit(215);
		}
		
		if (db_username == null) {
			System.exit(216);
		}
		
		if (db_password == null) {
			System.exit(217);
		}
		
		parameters = jm.getMacros();

		try {
			XMLReader reader = XMLReaderFactory
					.createXMLReader("org.apache.xerces.parsers.SAXParser");
			reader.setContentHandler(jcp);
			reader.parse(source);
		} catch (Exception ex) {
			// System.err.println("Unable to parse configuration: "
			// + ex.getMessage());
			System.exit(207);
		}

		reports = jcp.getReportsHash();
		parameters.putAll(jcp.getParametersHash()); // get the rest of the
		// parameters from the config file

		// go through all reports
		CustomJasperReport report = reports.get(report_name);

		if (report == null) {
			//System.err.println("Invalid report-name");
			System.exit(213);
		}
		
		JasperConnection jc = new JasperConnection(connection_string, db_username, db_password);
		
		// System.out.println(report.getName());
		report.generateReport(parameters, jc);

		switch (output_type) {

		case 0:
			report.generatePdf();
			break;

		case 1:
			report.generateCSV();
			break;

		case 2:
			report.generateJRXls();
			// report.generateJExcel();
			break;

		case 3:
			report.generateXhtml();
			// report.generateJExcel();
			break;

		case 4:
			report.generateDocx();
			// report.generateJExcel();
			break;

		}

		System.exit(0);

	}

	private static void printHelp() {
		System.out
				.println("USAGE: JasperEngine [-p <parameter1|value1;parameter2|value2;..parameterX|valueX] [-t <type>] [-h] <-n <report name>> < -d <connection_string>> < -u <db_username> > < -P <db_password> > < <config>\n");
		System.out
				.println("-t <type>  - The type of output, where type may be: PDF, CSV, XLS");
	}

}
