// Miscellaneous global functions for Caladis
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

// Distribution Object Array
function dist_obj(id,name,param, desc, link){
    this.id=id;
    this.name=name;
    this.param=param;
    this.desc=desc;
    this.link=link;
}

// construct objects corresponding to distribution types and descriptions
var dist = new Array();
dist.push( new dist_obj( 'norm', 'Normal', ['Mean', 'Standard Deviation'], 'The Normal distribution is a continuous probability distribution. It takes the shape of a bell and is symmetric about the mean.', 'en.wikipedia.org/wiki/Normal_distribution'));
dist.push( new dist_obj('unif', 'Uniform', ['Minimum', 'Maximum'], 'The Uniform distribution is a probability distribution for which all values between a lower and upper bound are equally probable.', 'en.wikipedia.org/wiki/Uniform_distribution_(continuous)'));
dist.push( new dist_obj( 'bino', 'Binomial', ['Population Size', 'Probability'], 'The Binomial distribution is a discrete probability distribution of the number of successes in a sequence of n independent yes/no experiments, each of which yields success with probability p.', 'en.wikipedia.org/wiki/Binomial_distribution'));
dist.push( new dist_obj( 'pois', 'Poisson', ['Lambda'], 'The Poisson distribution is a discrete probability distribution that expresses the probability of a given number of events occurring in a fixed interval of time and/or space if these events occur with a known average rate and independently of the time since the last event.', 'en.wikipedia.org/wiki/Poisson_distribution'));
dist.push( new dist_obj( 'duni', 'Discrete Uniform', ['Minimum', 'Maximum'], 'The discrete uniform distribution assigns equal probabilities to a range of possible integer values.', 'en.wikipedia.org/wiki/Uniform_distribution_(discrete)'));
dist.push( new dist_obj( 'logn', 'Log-normal', ['Mean', 'Standard Deviation'], 'The log-normal distribution is a continuous, long-tailed probability distribution that describes the values a quantity takes if the logarithm of that value is Normally distributed. The parameters you enter here will be the parameters of the log-normal distribution itself, rather than the underlying Normal distribution: see the following link for how to convert between these.', 'en.wikipedia.org/wiki/Log-normal_distribution'));
dist.push( new dist_obj( 'expo', 'Exponential', ['Lambda'], 'The Exponential distribution is a continuous probability distribution. It describes the time between events in a Poisson process, i.e. a process in which events occur continuously and independently at a constant average rate. It is the continuous analogue of the geometric distribution.', 'en.wikipedia.org/wiki/Exponential_distribution'));
dist.push( new dist_obj( 'gamm', 'Gamma', ['k', 'Theta'], 'The Gamma distribution is a continuous probability distribution that is related to the Beta distribution and arises naturally in processes for which the waiting times between Poisson distributed events are relevant.', 'en.wikipedia.org/wiki/Gamma_distribution'));
dist.push( new dist_obj( 'geom', 'Geometric', ['Probability'], 'The Geometric distribution is the probability distribution of the number of X Bernoulli trials needed to get one success, supported on the set { 1, 2, 3, ...}', 'en.wikipedia.org/wiki/Geometric_distribution'));
dist.push( new dist_obj( 'beta', 'Beta', ['Alpha', 'Beta'], 'The Beta distribution is a continuous probability distribution defined on the interval [0, 1] and parametrized by two positive shape parameters, Alpha and Beta.', 'en.wikipedia.org/wiki/Beta_distribution'));

//------------------------------------------------------------------
// Search
// Takes a global array of objects and an id value. Returns the 
// index at which the selector is found.
//------------------------------------------------------------------

function search( arr, id){
    for( var i=0; i<arr.length; i++ ){
        if( id == arr[i].id) return i;
    }
    return -1;
}