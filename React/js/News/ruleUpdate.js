class CashClass{
	constructor() {
        this.newsData  = WINDOWS_NEWS;        
    }	
	
	getConfig(){
	    return {
	    	newsData : this.newsData 
	    }    	
	}
	
	
	getData(){
		return{				
			newsData : this.newsData
	    } 
	}
}
var cash = new CashClass();
export default cash;