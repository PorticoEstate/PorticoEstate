import java.util.HashMap;
import java.util.StringTokenizer;

// This class contains static method(s) for generating JasperMacros in real-time.
class JasperMacros {

	private HashMap<String, String> macros;

	public JasperMacros() {
		this.macros = new HashMap<String, String>();
	}

	public HashMap<String, String> getMacros() {
		return this.macros;
	}

	// 'parameters' will be in the following format:
	// 'key1|value1;key2|value2;key3|value3' where key1, key2 ... keyX are
	// unique
	public void loadMacros(String parameters) {
		StringTokenizer st = new StringTokenizer(parameters, ";");
		while (st.hasMoreTokens()) {

			String[] parameter_value = st.nextToken().split("\\|");
			this.macros.put(parameter_value[0], parameter_value[1].trim());

		}

	}

}
