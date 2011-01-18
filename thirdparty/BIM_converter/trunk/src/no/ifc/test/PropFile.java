package no.ifc.test;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

public class PropFile {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		Properties props = new Properties();
		
		try {
			//InputStream inStream = Thread.currentThread().getContextClassLoader().getResourceAsStream("/WEB-INF/repository.properties");
			InputStream inStream = PropFile.class.getClassLoader().getResourceAsStream("WebContent/WEB-INF/repositories.properties");

			//props.load(new FileInputStream("WebContent/WEB-INF/repository.properties"));
			props.load(inStream);
			String message = props.getProperty("test");

            System.out.println(message);
		}catch(IOException e)
        {
            e.printStackTrace();
            }

	}

}
