package no.bimconverter.ifc;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import jsdai.lang.SdaiException;
import jsdai.lang.SdaiSession;
import jsdai.lang.SdaiTransaction;

public class IfcSdaiRepresentationImpl implements IfcSdaiRepresentation{
	private Logger logger = LoggerFactory.getLogger("no.bimconverter.ifc.IfcSdaiRepresentationImpl");
	protected SdaiSession session = null;
	protected SdaiTransaction transaction =null;
	protected Properties properties = new java.util.Properties();
	
	public IfcSdaiRepresentationImpl() {
		this(propertiesFileName);
	}
	public IfcSdaiRepresentationImpl(String propertiesFileNameArg) {
		InputStream inStream;
		try {
			//logger.debug("Opening following properties file:{}", Thread.currentThread().getContextClassLoader().getResource(propertiesFileNameArg).toString());
			//inStream =  Thread.currentThread().getContextClassLoader().getResourceAsStream(propertiesFileNameArg);
			inStream = getClass().getResourceAsStream( "/" +propertiesFileNameArg );
			//System.out.println(getClass().getResource("/").toString());
			//getClass().get
			if(inStream == null) {
				logger.error("Properties file not found!!");
				throw new FileNotFoundException();
			}
			properties.load(inStream);
			SdaiSession.setSessionProperties(properties);
		} catch (FileNotFoundException e1) {
			logger.error("Properties file not found!!");
			e1.printStackTrace();
			throw new IfcSdaiException("File not found!", e1);
		} catch (IOException e) {
			logger.error("IO Exception!!");
			e.printStackTrace();
			throw new IfcSdaiException("IO error!", e);
		} catch (SdaiException e) {
			logger.error("Sdai Exception!!");
			e.printStackTrace();
			throw new IfcSdaiException("Sdai error!", e);
		}
	}
	
	protected void openSdaiSession() {
		try {
			//session=SdaiSession.getSession();
			//session.closeSession();
			session=SdaiSession.openSession();
			//session = SdaiSession.getSession();
			transaction = session.startTransactionReadWriteAccess();
			
			
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new RepositoryExceptionUc("Could not create session, reason:"+e.getMessage(), e);
		}
	}
	protected void closeSdaiSession() {
		try {
			if(session != null)
				session.closeSession();
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	
}
