package no.bimconverter.ifc.v2x3;


import static junit.framework.Assert.assertNotNull;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;



import no.bimconverter.ifc.IfcTestMethods;
import org.junit.After;
import org.junit.Before;
import org.junit.Ignore;
import org.junit.Test;

public class WholeModelOutputTest extends IfcTestMethods{
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	private WholeModelOutput wholeModel= new WholeModelOutput();
	
	@Before
	public void setUp() {
		super.createTestRepo();
		
		wholeModel.load(model);
	}
	@After
	public void tearDown() {
		//repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testObject() throws JAXBException { 
		assertNotNull(wholeModel);
	}
	@Test
	@Ignore
	public void testDisplayCovering1() throws JAXBException {
		JAXBContext jc = JAXBContext.newInstance(WholeModelOutput.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(wholeModel, System.out );
	}

}
