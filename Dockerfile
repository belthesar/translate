FROM php:fpm
RUN apt update && apt install -y libxext6 libxrender1
RUN git clone https://github.com/unknownworlds/translate.git

