package TestJaxb;


import java.io.StringWriter;
import java.util.HashMap;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.Marshaller;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class AdaptersTest {

	@Before
	public void setUp() throws Exception {
	}

	@After
	public void tearDown() throws Exception {
	}
	
	@Test
	public void _map2()
	                throws Exception {

	        Map<String, Map<String, String>> dataStructure =
	                        new HashMap<String, Map<String, String>>();

	        Map<String, String> inner1 = new HashMap<String, String>();
	        Map<String, String> inner2 = new HashMap<String, String>();

	        dataStructure.put("a", inner1);
	        dataStructure.put("b", inner1);

	        inner1.put("a1", "1");
	        inner1.put("a2", "2");
	        inner2.put("b1", "1");
	        inner2.put("b2", "2");
/*
	        JAXBContext context = JAXBContext.newInstance(Adapters.XMap.class,
	                         Adapters.XEntry.class);

	        Marshaller marshaller = context.createMarshaller();
	        marshaller.setProperty(Marshaller.JAXB_FRAGMENT, true);
	        marshaller.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
*/
	       Adapters.XMap<String, Adapters.XMap<String, String>> yo = new  Adapters.XMap<String, Adapters.XMap<String, String>>();
	       Adapters.XMap<String, String> inner10 = new Adapters.XMap<String, String>();
	       inner10.add("yess", "asdf");
	       yo.add("test", inner10);
	       
	       JAXBContext jc = JAXBContext.newInstance(Adapters.XMap.class);
			Marshaller m = jc.createMarshaller();
			m.setProperty(Marshaller.JAXB_FRAGMENT, true);
			m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
			m.marshal( yo, System.out );
	        
/*
	        StringWriter sw = new StringWriter();

	        marshaller.marshal(dataStructure, sw);
	        System.out.println(sw.toString());
	        */
	}


}
