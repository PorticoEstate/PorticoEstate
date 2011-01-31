package TestJaxb;


import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class ParentTest {

	@Before
	public void setUp() throws Exception {
	}

	@After
	public void tearDown() throws Exception {
	}
	
	@Test
	public void test() throws JAXBException {
		Parent yo = new Parent();
		yo.children.add(new Child());
		JAXBContext jc = JAXBContext.newInstance(Parent.class);
		Marshaller m = jc.createMarshaller();
		 m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( yo, System.out );
	}

}
