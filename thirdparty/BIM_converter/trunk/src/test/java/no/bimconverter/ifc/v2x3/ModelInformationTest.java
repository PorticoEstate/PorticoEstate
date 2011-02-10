package no.bimconverter.ifc.v2x3;


import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.*;



import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class ModelInformationTest extends IfcTestMethods{

	
	private ModelInformation modelInformation;
	@Before
	public void setUp() {
		super.createTestRepo();
		this.modelInformation = model.getExchangeFileProperties();
	}
	@After
	public void tearDown() {
		//repo.deleteRepository(testingRepository);
	}
	@Test
	public void testDisplayObject() throws JAXBException {
		assertNotNull(this.modelInformation);
		JAXBContext jc = JAXBContext.newInstance(ModelInformation.class);
		Marshaller m = jc.createMarshaller();
		 m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( this.modelInformation, System.out );
		
	}
	@Test
	public void testValues() {
		assertEquals("Thomas Liebich", this.modelInformation.getAuthor());
		assertEquals("reference file created for the Basic FM Handover View", this.modelInformation.getAuthorization());
		assertNotNull(this.modelInformation.getChangeDate());
		assertEquals("ViewDefinition [CoordinationView, FMHandOverView]", this.modelInformation.getDescription());
		assertEquals("IFC2X3", this.modelInformation.getNativeSchema());
		assertEquals("AEC3", this.modelInformation.getOrganization());
		assertEquals("IFC text editor", this.modelInformation.getOriginatingSystem());
		assertEquals("IFC text editor", this.modelInformation.getPreProcessor());
	}

}
